<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class RecipeController extends Controller
{
    // ログイン必須
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * レシピ一覧
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $cat   = $request->query('cat');
        $fav   = $request->boolean('fav');
        $q     = trim((string) $request->query('q', ''));

        $query = Recipe::with(['category'])->where('user_id', $userId);

        if (!empty($cat)) {
            $query->where('recipe_category_id', $cat);
        }

        if ($fav) {
            $query->whereHas('favoritedByUsers', function ($sub) use ($userId) {
                $sub->where('users.id', $userId);
            });
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('ingredients', 'like', "%{$q}%");
            });
        }

        $recipes = $query
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        $categories = RecipeCategory::orderBy('id')->get();

        $latestRecipes = Recipe::with(['category'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('recipe.recipe_list', compact('recipes', 'categories', 'cat', 'fav', 'q', 'latestRecipes'));
    }

    /**
     * お気に入りON/OFF（中間テーブルの操作）
     */
    public function toggleFavorite(Request $request, Recipe $recipe)
    {
        $user = Auth::user();
        $isFavorited = $user->favoriteRecipes()->where('recipe_id', $recipe->id)->exists();

        if ($isFavorited) {
            $user->favoriteRecipes()->detach($recipe->id);
            $status = false;
        } else {
            $user->favoriteRecipes()->attach($recipe->id);
            $status = true;
        }

        return response()->json(['is_favorited' => $status]);
    }

    /**
     * レシピ編集画面（AIの提案に自分用メモを追記する用！）
     */
    public function edit($id)
    {
        $recipe = Recipe::where('user_id', auth()->id())->findOrFail($id);
        $categories = RecipeCategory::all();
        return view('recipe.recipe_update', compact('recipe', 'categories'));
    }

    /**
     * レシピ更新の保存処理
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'              => 'required|max:100',
            'recipe_category_id' => 'required|exists:recipe_categories,id',
            'memo'               => 'nullable',
            'ingredients'        => 'nullable',
        ]);

        $recipe = Recipe::where('user_id', auth()->id())->findOrFail($id);
        $recipe->recipe_category_id = $request->recipe_category_id;
        $recipe->title              = $request->title;
        $recipe->memo               = $request->memo;
        $recipe->ingredients        = $request->ingredients;

        $recipe->save();

        return redirect('/recipe_list')->with('success', 'レシピを更新しました');
    }

    /**
     * レシピ削除処理
     */
    public function destroy($id)
    {
        $recipe = Recipe::where('user_id', auth()->id())->findOrFail($id);
        $recipe->delete();

        return redirect('/recipe_list')->with('success', 'レシピを削除しました');
    }

    /**
     * レシピ詳細
     */
    public function show(Request $request, $id)
    {
        $recipe = Recipe::where('user_id', auth()->id())->with(['category'])->findOrFail($id);

        $isFavorited = auth()->user()
            ->favoriteRecipes()
            ->where('recipes.id', $recipe->id)
            ->exists();

        return view('recipe.detail', compact('recipe', 'isFavorited'));
    }

// =========================================================
    // AI献立提案機能
    // =========================================================

    /**
     * AI提案画面（Geminiと通信）
     */
    public function suggest(Request $request)
    {
        // 1. 冷蔵庫の在庫を取得する（自分の食材）
        $ingredients = Ingredient::where('user_id', auth()->id())->pluck('name')->toArray();

        // もし冷蔵庫が空っぽなら、お助けデフォルト食材を渡す
        $stockList = empty($ingredients) ? '豚肉、キャベツ、玉ねぎ、卵' : implode('、', $ingredients);

        // 2.Geminiへのプロンプト
        $prompt = "
        あなたはプロの料理研究家であり、子育て中のママの強い味方です。
        以下の「冷蔵庫の食材」を使って、子供も喜ぶ美味しいレシピを1つ提案してください。
        必ず以下のJSON形式のみで出力してください。Markdownのコードブロック(```json等)は絶対に付けないでください。

        【冷蔵庫の食材】
        {$stockList}

        【出力フォーマット（JSON）】
        {
            \"title\": \"レシピのタイトル（最大30文字、オシャレな感じで！）\",
            \"ingredients\": \"使う材料と分量（改行は\\nを使用）\",
            \"kids_tips\": \"子供が食べやすくなる魔法の工夫（50文字程度）\",
            \"nutrition\": {
                \"タンパク質\": 3,
                \"ビタミン\": 4,
                \"カルシウム\": 2,
                \"鉄分\": 3,
                \"エネルギー\": 4
            },
            \"steps\": [
                \"手順1（具体的に）\",
                \"手順2\",
                \"手順3\",
                \"手順4\"
            ],
            \"memo\": \"大人向けの味変や、ちょっとしたアレンジのアイデア\"
        }
        ";

        //3.Gemini API の呼び出し
        $apiKey = env('GEMINI_API_KEY');

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ]
            ]);

            // 通信成功したら、AIの返事を配列に変換
            if ($response->successful()) {
                $resultText = $response->json('candidates.0.content.parts.0.text');
                $dummyAiRecipe = json_decode($resultText, true);

                if (!$dummyAiRecipe) {
                    throw new \Exception("AIの返事がJSON形式ではありませんでした。");
                }
            } else {
                // APIエラー時は例外を投げてcatchに拾ってもらう
                throw new \Exception("API通信エラー");
            }
        } catch (\Exception $e) {
            // お知らせ画面
            $dummyAiRecipe = [
                'title'       => '【お知らせ】お野菜たっぷりホイコーロー風',
                'ingredients' => "豚バラ肉: 200g\nキャベツ: 1/4玉",
                'kids_tips'   => "申し訳ございません。現在AIシェフが席を外しております☕️ 少しお時間を置いてから再度お試しくださいませ✨",
                'nutrition'   => ['タンパク質' => 3, 'ビタミン' => 3, 'カルシウム' => 3, '鉄分' => 3, 'エネルギー' => 3],
                'steps'       => ['AIシェフの帰りを待っています...'],
                'memo'        => "APIキーの設定や、サーバーの再起動をご確認いただけますと幸いです！",
            ];
        }


        return view('recipe.suggest', compact('dummyAiRecipe'));
    }

    /**
     * 提案されたレシピをDBに保存する処理
     */
    public function saveSuggestion(Request $request)
    {
        // 1. Bladeの隠しフィールドから、AIのレシピデータ（JSON）を受け取って配列に戻す
        $aiData = json_decode($request->input('ai_recipe_data'), true);

        if (!$aiData) {
            return back()->with('error', 'データの保存に失敗しました💦');
        }

        // 2. とりあえず一番最初のカテゴリー（「メイン」など）を取得（無ければ1にする）
        $defaultCategory = \App\Models\RecipeCategory::first();

        // 3. AIのデータを、DBの形に合わせて整理

        // 材料を入れる
        $memoContent = "🛒 【使う材料】\n" . $aiData['ingredients'] . "\n\n";

        // キッズ向けの工夫
        $memoContent .= "✨ 【AIシェフのキッズ向け工夫】\n" . $aiData['kids_tips'] . "\n\n";


        // 作り方を順番に入れる！
        $memoContent .= "🍳 【作り方】\n";
        foreach ($aiData['steps'] as $index => $step) {
            $memoContent .= ($index + 1) . ". " . $step . "\n";
        }

        // 最後に大人向けアレンジ！
        $memoContent .= "\n🍷 【大人向けアレンジ】\n" . $aiData['memo'];

        // 4. データベースに新しいレシピとして保存！
        $recipe = new Recipe();
        $recipe->user_id = auth()->id();
        $recipe->recipe_category_id = $defaultCategory->id ?? 1; // とりあえず最初のカテゴリ
        $recipe->title = $aiData['title'];
        $recipe->memo = $memoContent; // ぜんぶ詰めた巨大なメモをここにIN！
        $recipe->save();

        // 5. 保存したら一覧画面へリダイレクト！
        return redirect('/recipe_list')->with('success', 'AIの提案をレシピ帳に保存しました！📖✨');
    }
}
