<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IngredientController;
use App\Models\Ingredient;
use Symfony\Component\Routing\Route as RoutingRoute;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\UserRegisterController;
use App\Http\Controllers\RecipeFavoriteController;
use Illuminate\Support\Facades\Auth;

//Laravel welcome画面をログイン画面へリダイレクト
Route::redirect('/', '/login');

// ユーザー新規作成
Route::get('/user_register', [UserRegisterController::class, 'index'])->name('user_register');
//ユーザー新規登録画面の登録ボタン
Route::post('/user_register', [UserRegisterController::class, 'store'])->name('user_store');
// パスワード再設定（通知のみ）
Route::view('/password/change', 'auth.password_unavailable')->name('password.request2');
// もし /password/reset にアクセスされたら /password/change へ
Route::redirect('/password/reset', '/password/change');

Auth::routes();

//未ログインユーザーを排除
Route::middleware('auth')->group(function () {

    //ユーザー関連
    //ユーザー一覧画面
    Route::get('/user_list', [UserRegisterController::class, 'list'])->name('user_list');
    //ユーザー編集画面用
    Route::get('/user_edit/{id}', [UserRegisterController::class, 'edit']);
    //ユーザー編集画面の変更を保存するボタン
    Route::put('/user_update/{id}', [UserRegisterController::class, 'update'])->name('user_update');
    //ユーザー編集画面のユーザーを削除するボタン
    Route::delete('/user_delete/{id}', [UserRegisterController::class, 'destroy'])->name('user_delete');
   
    
    //Home画面
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    //食材関連
    //[食材一覧画面]
    Route::get('/ingredient_list', [IngredientController::class, 'index'])
        ->name('ingredient.list');
    //[食材新規登録画面]
    Route::get('/ingredient_register', [IngredientController::class, 'create'])->name('ingredient.register');
    //[新規登録の保存処理(POST送信)]
    Route::post('/ingredients', [IngredientController::class, 'store']);
    //[食材の削除処理(POST送信)]
    Route::post('/ingredient_delete/{id}', [IngredientController::class, 'destroy']);
    //[食材編集画面]
    Route::get('/ingredient_update/{id}', [IngredientController::class, 'edit']);
    // 食材関連 [食材を上書き保存する処理]
    Route::put('/ingredient_update/{id}', [IngredientController::class, 'update']);

    //レシピ関連
    //[レシピ一覧]
    Route::get('/recipe_list', [RecipeController::class, 'index'])->name('recipe.list');
    //[レシピ詳細]
    Route::get('/recipe_detail/{id}', [RecipeController::class, 'show'])->name('recipe.detail');
    //[お気に入りトグル]
    Route::post('/recipes/{recipe}/favorite', [RecipeFavoriteController::class, 'toggle'])
        ->name('recipes.favorite.toggle');
    //[レシピ登録画面]
    Route::get('/recipe_register', [RecipeController::class, 'register']);
    //[レシピ登録の保存処理]
    Route::post('/recipe_register', [RecipeController::class, 'store']);
    //[レシピ編集画面]
    Route::get('/recipe_update/{id}', [RecipeController::class, 'edit']);
    //[レシピ更新の保存処理]
    Route::patch('/recipe_update/{id}', [RecipeController::class, 'update']);
    //[レシピ削除処理]
    Route::delete('/recipe_update/{id}', [RecipeController::class, 'destroy']);

});

// サンプル画面
Route::get('/sample', function () {
    return view('sample');
});




