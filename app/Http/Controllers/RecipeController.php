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

        // 左カラムに表示する「使う材料」のリスト（これは全部出す）
        $ingredientsList = $recipeIngredients->map(function ($row) {
            return $row->quantity ? "{$row->name}：{$row->quantity}" : $row->name;
        })->toArray();

        // 在庫消費モーダルには出さない「基本調味料」の除外リスト！
        $ignoreList = ['水', 'お湯', '醤油', '塩', '砂糖', 'こしょう', 'コショウ', '胡椒', '油', 'サラダ油', 'ごま油', 'オリーブオイル', '酒', '料理酒', 'みりん', '酢', 'ケチャップ', 'マヨネーズ', 'バター', '片栗粉', '小麦粉'];

        // モーダル用には、調味料を省いた「オブジェクトのまま」の綺麗なデータを渡す！
        $modalIngredients = $recipeIngredients->filter(function ($row) use ($ignoreList) {
            return !in_array($row->name, $ignoreList);
        });

        // Viewに $modalIngredients も渡す！
        return view('recipe.detail', compact('recipe', 'isFavorited', 'ingredientsList', 'modalIngredients'));
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

        // マンネリ防止！順番をシャッフルして、AIには「ランダムな4つ」だけを見せる！
        shuffle($ingredients);
        $selectedIngredients = array_slice($ingredients, 0, 4); // 4つだけ抜き出す！

        $stockList = empty($selectedIngredients) ? '豚肉、キャベツ、玉ねぎ、卵' : implode('、', $selectedIngredients);

        $prompt = "
        あなたはプロの料理研究家であり、幼稚園児のお弁当作りに悩むママの強い味方です。
        以下の「冷蔵庫の食材」から相性の良いものを選んで、子供が喜ぶ【幼稚園のお弁当のおかず】を1つ提案してください。

        【絶対に守るべきお弁当のルール（厳守！）】
        1. 日本の一般的なお弁当のおかずとして違和感がないこと。冷めても美味しいこと。
        2. 忙しい朝でも「15分以内」で作れる現実的なレシピであること。
        3. 【重要】絵文字（🧂、🥦など）は文字化けしてエラーになるため、出力全体を通して一切使用しないでください。
        4. 【重要】「材料（ingredients）」には、以下の【冷蔵庫の食材】の名称を**一言一句変えずにそのまま**使用してください。（例：「ブロッコリ」を「ブロッコリー」と勝手に書き換えないこと！）
        5. 【重要】毎回同じ食材（ウィンナーや卵など）ばかりにならないよう、和・洋・中などバリエーション豊かな味付けや調理法を提案してください。

        【冷蔵庫の食材（※この名称を一言一句変えないこと！）】
        {$stockList}

        【出力フォーマット（JSON）】
        必ず以下の構造を厳守してください。配列のカッコ（ [ と ] ）を絶対に間違えないこと！Markdown(```json)は不要です。
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
                    throw new \Exception("AIからの返答が空っぽでした（生データ: " . $rawResponse . "）");
                }

                // 念のためのフィルター（Markdown除去）
                $resultText = preg_replace('/```json\s*(.*?)\s*```/is', '$1', $resultText);
                $resultText = str_replace('```', '', $resultText);

                // AIの構文エラーをカバー
                $resultText = preg_replace('/\}\s*,\s*"memo"/is', '], "memo"', $resultText);

                $dummyAiRecipe = json_decode(trim($resultText), true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $jsonError = json_last_error_msg();
                    throw new \Exception("JSON変換エラー ({$jsonError}) \n【AIの実際の返答】\n" . $resultText);
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
                'ingredients' => [
                    ['name' => '豚バラ肉', 'quantity' => '200g'],
                    ['name' => 'キャベツ', 'quantity' => '1/4玉']
                ],
                'kids_tips'   => "AIシェフが休憩中です。再度お試しください",
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

        // テキストの切り分け（explode）をやめて、配列から直接取り出す！
        if (!empty($aiData['ingredients']) && is_array($aiData['ingredients'])) {
            foreach ($aiData['ingredients'] as $ingData) {

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

    // =========================================================
    // レシピを作った後の在庫消費処理
    // =========================================================
    public function consumeIngredients(Request $request, Recipe $recipe)
    {
        $items = $request->input('items');

        if (empty($items) || !is_array($items)) {
            return redirect('/home')->with('error', 'データの更新に失敗しました。');
        }

        foreach ($items as $item) {
            $name = $item['name'] ?? '';
            $action = $item['action'] ?? '';

            // ユーザーの食材在庫を特定
            $ing = Ingredient::where('name', $name)
                ->where('user_id', auth()->id())
                ->first();

            if (!$ing) continue; // 在庫がなければスルー

            // ユーザーの選択をDBに反映
            if ($action === 'delete') {
                // アクションが「使い切った」なら、在庫を無くす（論理削除）
                $ing->delete_flg = 1;
                $ing->save();
            }
            // アクションが「残す（keep）」なら、何もしない（在庫数は現状維持）
        }

        // 処理が終わったら、オシャレなダッシュボード（ホーム）にリダイレクト
        return redirect('/home')->with('success', '冷蔵庫の在庫を更新しました！料理お疲れ様でした✨');
    }
}
