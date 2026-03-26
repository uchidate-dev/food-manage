<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RecipeController extends Controller
{   
    /**
     * レシピ登録画面
     */
    public function register()
    {
        $categories = RecipeCategory::all();
        return view('recipe.recipe_register', compact('categories'));
    }

    /**
     * レシピ登録の保存処理
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'              => 'required|max:100',
            'recipe_category_id' => 'required|exists:recipe_categories,id',
            'url'                => 'nullable|url|max:2048',
            'memo'               => 'nullable',
            'ingredients'        => 'nullable',
            'finished_image'     => 'nullable',
            'step1'              => 'nullable',
        ]);

        $recipe = new Recipe();

        // ログイン機能実装後に Auth::id() に変更する
        $recipe->user_id            = auth()->id();
        $recipe->recipe_category_id = $request->recipe_category_id;
        $recipe->title              = $request->title;
        $recipe->url                = $request->url;
        $recipe->memo               = $request->memo;
        $recipe->ingredients        = $request->ingredients;
        $recipe->finished_image     = $request->finished_image ?? null;
        $recipe->private_flg        = $request->has('private_flg') ? 0 : 1;
        $recipe->favorite_flg       = 0;

        // 手順・手順画像（step1〜step15）
        for ($i = 1; $i <= 15; $i++) {
            $stepKey      = "step{$i}";
            $stepImageKey = "step_image{$i}";
            $recipe->$stepKey      = $request->$stepKey ?? null;
            $recipe->$stepImageKey = $request->$stepImageKey ?? null;
        }

        $recipe->save();

        return redirect('/recipe_list')->with('success', 'レシピを登録しました');
    }

    /**
     * レシピ編集画面
     */
    public function edit($id)
    {
        $recipe = Recipe::findOrFail($id);
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
            'url'                => 'nullable|url|max:2048',
            'memo'               => 'nullable',
            'ingredients'        => 'nullable',
            'finished_image'     => 'nullable',
            'step1'              => 'nullable',
        ]);

        $recipe = Recipe::findOrFail($id);
        $recipe->recipe_category_id = $request->recipe_category_id;
        $recipe->title               = $request->title;
        $recipe->url                 = $request->url;
        $recipe->memo                = $request->memo;
        $recipe->ingredients         = $request->ingredients;
        $recipe->private_flg         = $request->has('private_flg') ? 0 : 1;

        // 画像が空の場合はnullで上書き（削除対応）
        $recipe->finished_image = $request->finished_image ?: null;

        // 手順・手順画像（step1〜step15）
        for ($i = 1; $i <= 15; $i++) {
            $stepKey      = "step{$i}";
            $stepImageKey = "step_image{$i}";
            $recipe->$stepKey = $request->$stepKey ?? null;
            $recipe->$stepImageKey = $request->$stepImageKey ?: null;
        }

        $recipe->save();

        return redirect('/recipe_list')->with('success', 'レシピを更新しました');
    }

    /**
     * レシピ削除処理
     */
    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->delete();

        return redirect('/recipe_list')->with('success', 'レシピを削除しました');
    }

    /**
     * レシピ一覧
     * - 公開レシピ + 自分の非公開（ログイン時）
     * - フィルタ：scope / cat / fav / q
     * - Newレシピ：他ユーザー作成、1か月以内、作成日が新しい順、10件
     */
    public function index(Request $request)
    {
        $userId = Auth::id(); // 未ログインなら null

        // フィルタ（URLクエリ）
        $scope = $request->query('scope', 'all'); // all | mine
        $cat   = $request->query('cat');          // recipe_category_id
        $fav   = $request->boolean('fav');        // true/false
        $q     = trim((string) $request->query('q', ''));

        $query = Recipe::query()
            ->with(['user', 'category']);

        // 公開制御：他人は公開のみ／自分は全部（ログイン時）
        $query->where(function ($w) use ($userId) {
            $w->where('private_flg', 0);
            if ($userId) {
                $w->orWhere('user_id', $userId);
            }
        });

        // ALL / MyRecipe
        if ($scope === 'mine') {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                // 未ログインで mine は0件
                $query->whereRaw('1 = 0');
            }
        }

        // カテゴリ絞り込み
        if (!empty($cat)) {
            $query->where('recipe_category_id', $cat);
        }

        // お気に入り絞り込み：pivotのみを見る
        if ($fav) {
            if ($userId) {
                $query->whereHas('favoritedByUsers', function ($sub) use ($userId) {
                    $sub->where('users.id', $userId);
                });
            } else {
                // 未ログインで fav は0件
                $query->whereRaw('1 = 0');
            }
        }

        // 検索（タイトル＋材料）
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

        // 一覧の現在URLを保存（詳細から戻る用）
        session(['recipes_back_url' => url()->full()]);

        // Newレシピ（他ユーザー / 1か月以内 / 新しい順 / 10件）
        $latestRecipes = collect();

        if ($userId) {
            $latestRecipes = Recipe::query()
                ->with(['user', 'category'])
                ->where('user_id', '!=', $userId)
                ->where('created_at', '>=', Carbon::now()->subMonth())
                ->where('private_flg', 0)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        return view('recipe.list', compact(
            'recipes',
            'categories',
            'scope',
            'cat',
            'fav',
            'q',
            'latestRecipes'
        ));
    }

    /**
     * お気に入りON/OFF（updated_atを更新しない）
     */
    public function toggleFavorite(Recipe $recipe)
    {
        $recipe->timestamps = false; // updated_at を更新しない
        $recipe->favorite_flg = $recipe->favorite_flg ? 0 : 1;
        $recipe->save();

        return response()->json([
            'favorite_flg' => $recipe->favorite_flg
        ]);
    }

    /**
     * レシピ詳細
     * - category() リレーションで統一
     * - 画像は base64本体 -> data URI に変換して Bladeへ渡す
     */
    public function show(Request $request, $id)
    {
        $recipe = Recipe::with(['category', 'user'])->findOrFail($id);

        // 戻り先決定
        if ($request->filled('back')) {
            // 一覧の検索状態などを保持
            $backUrl = $request->query('back');
        } elseif ($request->query('from') === 'home') {
            // ホームから来た場合
            $backUrl = url('/home');
        } else {
            // その他（直アクセスなど）
            $backUrl = url()->previous();
        }

        session(['recipes_back_url' => $backUrl]);

        // 閲覧履歴（ログ型：開くたびに1レコード）
        if (auth()->check()) {
            \App\Models\RecipeView::create([
                'user_id'   => auth()->id(),
                'recipe_id' => $recipe->id,
                'viewed_at' => now(),
            ]);
        }

        // お気に入り判定（ユーザーごと）
        $isFavorited = false;
        if (auth()->check()) {
            $isFavorited = auth()->user()
                ->favoriteRecipes()
                ->where('recipes.id', $recipe->id)
                ->exists();
        }

        // 完成画像
        $recipe->finished_image = $this->imgSrcFromBase64($recipe->finished_image);

        // 手順画像 1〜15
        for ($i = 1; $i <= 15; $i++) {
            $key = "step_image{$i}";
            $recipe->$key = $this->imgSrcFromBase64($recipe->$key);
        }

        return view('recipe.detail', compact('recipe', 'isFavorited'));
    }

    /**
     * base64（本体のみ） -> data URI
     * すでに data:image/... 形式ならそのまま
     */
    private function imgSrcFromBase64(?string $b64): ?string
    {
        if (!$b64) {
            return null;
        }

        // すでに data URI ならそのまま
        if (str_starts_with($b64, 'data:image/')) {
            return $b64;
        }

        $head = substr($b64, 0, 10);
        $mime = 'image/jpeg';

        // ざっくり判定（先頭シグネチャ）
        if (str_starts_with($head, 'iVBORw0KG')) {
            $mime = 'image/png';
        } elseif (str_starts_with($head, '/9j/')) {
            $mime = 'image/jpeg';
        } elseif (str_starts_with($head, 'R0lGOD')) {
            $mime = 'image/gif';
        } elseif (str_starts_with($head, 'UklGR')) {
            $mime = 'image/webp';
        }

        return "data:{$mime};base64,{$b64}";
    }
}