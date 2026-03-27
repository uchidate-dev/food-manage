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
}
