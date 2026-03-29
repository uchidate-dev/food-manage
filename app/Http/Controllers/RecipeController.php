<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

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
                    ->orWhereExists(function ($subQuery) use ($q) {
                        // 中間テーブルからキーワード検索
                        $subQuery->select(DB::raw(1))
                            ->from('recipe_ingredients')
                            ->join('ingredients', 'recipe_ingredients.ingredient_id', '=', 'ingredients.id')
                            ->whereColumn('recipe_ingredients.recipe_id', 'recipes.id')
                            ->where('ingredients.name', 'like', "%{$q}%");
                    });
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
     * レシピ編集画面
     */
    public function edit($id)
    {
        $recipe = Recipe::where('user_id', auth()->id())->findOrFail($id);
        $categories = RecipeCategory::all();

        //  中間テーブルのデータをテキストに戻して画面に渡す
        $recipeIngredients = DB::table('recipe_ingredients')
            ->join('ingredients', 'recipe_ingredients.ingredient_id', '=', 'ingredients.id')
            ->where('recipe_ingredients.recipe_id', $recipe->id)
            ->select('ingredients.name', 'recipe_ingredients.quantity')
            ->get();

        $recipe->ingredients = $recipeIngredients->map(function ($row) {
            return $row->quantity ? "{$row->name}：{$row->quantity}" : $row->name;
        })->implode("\n");

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
            'kids_tips'          => 'nullable',
            'steps'              => 'nullable',
        ]);

        $recipe = Recipe::where('user_id', auth()->id())->findOrFail($id);
        $recipe->recipe_category_id = $request->recipe_category_id;
        $recipe->title              = $request->title;
        $recipe->memo               = $request->memo;
        $recipe->kids_tips          = $request->kids_tips;

        // stepsはテキストエリアから改行区切りの文字列で来るので、配列に変換して保存
        $stepsArray = [];
        if (!empty($request->steps)) {
            // 改行で分割して、空の行を取り除き、配列にする
            $stepsArray = array_values(array_filter(array_map('trim', explode("\n", $request->steps))));
        }
        $recipe->steps = $stepsArray;

        $recipe->save();

        // 一旦古い材料をリセットして、新しいものを中間テーブルに登録
        DB::table('recipe_ingredients')->where('recipe_id', $recipe->id)->delete();

        if (!empty($request->ingredients)) {
            $lines = explode("\n", $request->ingredients);
            foreach ($lines as $line) {
                // ここにも保存時と同じように絵文字や記号を弾くフィルターを入れると安心！
                $line = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $line);
                $line = preg_replace('/^[\・\-\*\s]+/u', '', $line);
                $line = trim($line);

                if (!$line) continue;

                $parts = preg_split('/[:：\s　]+/', $line, 2);
                $name = trim($parts[0] ?? '');
                $qty = trim($parts[1] ?? '');

                if ($name) {
                    $ing = Ingredient::where('name', $name)->where('user_id', auth()->id())->first();
                    if (!$ing) {
                        $ing = new Ingredient();
                        $ing->user_id = auth()->id();
                        $ing->ingredient_cat_id = 9;
                        $ing->name = $name;
                        $ing->storage_type = 0;
                        $ing->quantity = '0';
                        $ing->delete_flg = 0;
                        $ing->save();
                    }
                    DB::table('recipe_ingredients')->updateOrInsert(
                        ['recipe_id' => $recipe->id, 'ingredient_id' => $ing->id],
                        ['quantity' => mb_substr($qty, 0, 50), 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }

        return redirect('/recipe_list')->with('success', 'レシピを更新しました');
    }
    /**
     * レシピ削除処理
     */
    public function destroy($id)
    {
        $recipe = Recipe::where('user_id', auth()->id())->findOrFail($id);
        $recipe->delete(); // ※ cascadeOnDelete があるので中間テーブルも自動で消える

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

        // 中間テーブルのデータを取得
        $recipeIngredients = DB::table('recipe_ingredients')
            ->join('ingredients', 'recipe_ingredients.ingredient_id', '=', 'ingredients.id')
            ->where('recipe_ingredients.recipe_id', $recipe->id)
            ->select('ingredients.name', 'recipe_ingredients.quantity')
            ->get();

        // 🌟 ここを変更！テキストに繋げるのではなく、配列にする！
        $ingredientsList = $recipeIngredients->map(function ($row) {
            return $row->quantity ? "{$row->name}：{$row->quantity}" : $row->name;
        })->toArray();

        $searchQuery = urlencode($recipe->title . " お弁当 幼稚園");

        // Viewに 新しく作った $ingredientsList を渡す！
        return view('recipe.detail', compact('recipe', 'isFavorited', 'searchQuery', 'ingredientsList'));
    }

    // =========================================================
    // AI献立提案機能
    // =========================================================

    public function suggest(Request $request)
    {
        $ingredients = Ingredient::where('user_id', auth()->id())
            ->where('delete_flg', 0)
            ->pluck('name')
            ->toArray();

        $stockList = empty($ingredients) ? '豚肉、キャベツ、玉ねぎ、卵' : implode('、', $ingredients);

        $prompt = "
        あなたはプロの料理研究家であり、幼稚園児のお弁当作りに悩むママの強い味方です。
        以下の「冷蔵庫の食材」から相性の良いものを選んで、子供が喜ぶ【幼稚園のお弁当のおかず】を1つ提案してください。
        必ず以下のJSON形式のみで出力してください。Markdownのコードブロック(```json等)は絶対に付けないでください。

        【絶対に守るべきお弁当のルール（厳守！）】
        1. 日本の一般的なお弁当のおかずとして違和感がないこと。冷めても美味しいこと。
        2. 忙しい朝でも「15分以内」で作れる現実的なレシピであること。
        3. 【重要】絵文字（🧂、🥦、✨、🤏など）はデータベースで文字化けしてエラーになるため、出力全体を通して**一切使用しないでください**。すべて通常の文字で書いてください（例：「🤏ひとつまみ」→「ひとつまみ」）。
        4. 【重要】「作り方（steps）」に登場する食材は、必ず「材料（ingredients）」にも漏れなく記載してください（書き忘れ厳禁！）。

        【冷蔵庫の食材】
        {$stockList}

        【出力フォーマット（JSON）】
        必ず以下の構造を厳守してください。配列のカッコ（ [ と ] ）を絶対に間違えないこと！
        {
            \"title\": \"レシピのタイトル（最大30文字）\",
            \"ingredients\": [
                {\"name\": \"食材名1\", \"quantity\": \"分量1\"},
                {\"name\": \"食材名2\", \"quantity\": \"分量2\"}
            ],
            \"kids_tips\": \"子供が食べやすくなる魔法の工夫\",
            \"nutrition\": {
                \"タンパク質\": 3, \"ビタミン\": 4, \"カルシウム\": 2, \"鉄分\": 3, \"エネルギー\": 4
            },
            \"steps\": [\"手順1\", \"手順2\", \"手順3\"],
            \"memo\": \"大人用のお弁当に入れる際の味変アレンジ\"
        }
        ";

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

            if ($response->successful()) {
                $responseData = $response->json();

                $resultText = data_get($responseData, 'candidates.0.content.parts.0.text');

                if (empty($resultText)) {
                    $rawResponse = json_encode($responseData, JSON_UNESCAPED_UNICODE);
                    throw new \Exception("AIからの返答が空っぽでした💦（生データ: " . $rawResponse . "）");
                }

                // 念のためのフィルター（Markdown除去）
                $resultText = preg_replace('/```json\s*(.*?)\s*```/is', '$1', $resultText);
                $resultText = str_replace('```', '', $resultText);

                // 🌟🌟🌟 ここを追加！AIのドジ（構文エラー）をカバーする安全網！ 🌟🌟🌟
                // steps配列の最後を } で閉じちゃうAIのクセを、強制的に ] に修正します！
                $resultText = preg_replace('/\}\s*,\s*"memo"/is', '], "memo"', $resultText);

                $dummyAiRecipe = json_decode(trim($resultText), true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $jsonError = json_last_error_msg();
                    throw new \Exception("JSON変換エラー ({$jsonError}) 🚨\n【AIの実際の返答】\n" . $resultText);
                }

                if (!$dummyAiRecipe) {
                    throw new \Exception("AIの返事がうまく読み込めませんでした。");
                }
            } else {
                $errorBody = $response->body();
                throw new \Exception("API通信エラー: " . $errorBody);
            }
        } catch (\Exception $e) {
            $dummyAiRecipe = [
                'title'       => '【お知らせ】お野菜たっぷりホイコーロー風',
                'ingredients' => "豚バラ肉: 200g\nキャベツ: 1/4玉",
                'kids_tips'   => "AIシェフが休憩中です。再度お試しください✨",
                'nutrition'   => ['タンパク質' => 3, 'ビタミン' => 3, 'カルシウム' => 3, '鉄分' => 3, 'エネルギー' => 3],
                'steps'       => ['AIシェフの帰りを待っています...'],
                'memo'        => "【開発者用エラーログ】\n" . $e->getMessage(),
            ];
        }

        return view('recipe.suggest', compact('dummyAiRecipe'));
    }

    public function saveSuggestion(Request $request)
    {
        $aiData = json_decode($request->input('ai_recipe_data'), true);

        if (!$aiData) {
            return back()->with('error', 'データの保存に失敗しました💦');
        }

        $defaultCategory = RecipeCategory::first();

        $recipe = new Recipe();
        $recipe->user_id = auth()->id();
        $recipe->recipe_category_id = $defaultCategory->id ?? 1;
        $recipe->title = $aiData['title'];
        $recipe->memo = $aiData['memo'];
        $recipe->kids_tips = $aiData['kids_tips'];
        $recipe->steps = $aiData['steps'];
        $recipe->nutrition = $aiData['nutrition'];
        $recipe->save();

        // AIのテキストを分解して中間テーブル（recipe_ingredients）に保存
        if (!empty($aiData['ingredients']) && is_array($aiData['ingredients'])) {
            foreach ($aiData['ingredients'] as $ingData) {
                // 配列から直接「名前」と「分量」を取り出す！
                $name = trim($ingData['name'] ?? '');
                $qty = trim($ingData['quantity'] ?? '');

                if ($name) {
                    $ing = Ingredient::where('name', $name)->where('user_id', auth()->id())->first();
                    if (!$ing) {
                        $ing = new Ingredient();
                        $ing->user_id = auth()->id();
                        $ing->ingredient_cat_id = 9;
                        $ing->name = $name;
                        $ing->storage_type = 0;
                        $ing->quantity = '0';
                        $ing->delete_flg = 0;
                        $ing->save();
                    }
                    DB::table('recipe_ingredients')->updateOrInsert(
                        ['recipe_id' => $recipe->id, 'ingredient_id' => $ing->id],
                        ['quantity' => mb_substr($qty, 0, 50), 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }

        return redirect('/recipe_list')->with('success', 'AIの提案をレシピ帳に保存しました！📖✨');
    }
}
