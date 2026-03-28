<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    // 🤖 AI献立提案機能
    // =========================================================

    /**
     * AI提案画面（まずはダミーデータで画面デザイン）
     */
    public function suggest(Request $request)
    {
        // ※後でここは「本物のAI（API）からの回答」に差し替えます！
        // 今は画面をデザインするために、理想的なAIの返事を手書きで用意しておきます。

        $dummyAiRecipe = [
            'title'       => 'お肉やわらか！お野菜たっぷりホイコーロー風',
            'category_id' => 3, // 中華など
            'ingredients' => "豚バラ肉: 200g\nキャベツ: 1/4玉\nにんじん: 1/3本\nピーマン: 1個\nごま油: 大さじ1\n【合わせ調味料】\n味噌: 大さじ1\n醤油: 小さじ1\n砂糖: 小さじ1",
            'memo'        => "大人はお皿に盛った後、豆板醤やラー油を少し垂らすと本格的な味になります！",

            // 子供の食べやすい工夫
            'kids_tips'   => "豚肉に薄く片栗粉をまぶして焼くことで、パサパサせず子供でも噛み切りやすくなります！野菜は炒める前にレンジで2分チンしておくと、甘みが増してピーマンの苦味も消えるのでペロリと食べてくれますよ✨",

            // 栄養グラフ用のデータ（5段階評価）
            'nutrition'   => [
                'タンパク質' => 4,
                'ビタミン'   => 5,
                'カルシウム' => 2,
                '鉄分'       => 3,
                'エネルギー' => 4,
            ],

            // 手順は配列で持っておくと、画面で番号(1,2,3...)を振りやすい
            'steps'       => [
                'キャベツ、にんじん、ピーマンは子供が食べやすい小さめの乱切りにし、耐熱ボウルに入れてふんわりラップをし、600Wで2分加熱する。',
                '豚バラ肉は3cm幅に切り、ポリ袋に入れて片栗粉を薄くまぶす。',
                'フライパンにごま油を熱し、豚肉を炒める。色が変わったら、1の野菜を汁気を切って加える。',
                '全体に油が回ったら、【合わせ調味料】を入れてサッと炒め合わせれば完成！',
            ],
        ];

        // recipeフォルダーの中に suggest.blade.php を作る想定
        return view('recipe.suggest', compact('dummyAiRecipe'));
    }

    /**
     * 提案されたレシピをDBに保存する処理（今はまだ空っぽでOK）
     */
    public function saveSuggestion(Request $request)
    {
        // 画面ができたら、ここでAIのデータをRecipeモデルに保存する処理を書きます！
        // 保存したら、詳細画面へリダイレクト！
        return redirect('/recipe_list')->with('success', 'AIの提案をレシピ帳に保存しました！');
    }
}
