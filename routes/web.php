<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserRegisterController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;

// ==========================================
//  認証・未ログインエリア
// ==========================================

// Laravel welcome画面をログイン画面へリダイレクト
Route::redirect('/', '/login');

// デフォルトの認証機能（ログイン等）
Auth::routes();

// パスワード再設定（通知のみ）
Route::view('/password/change', 'auth.password_unavailable')->name('password.request2');
Route::redirect('/password/reset', '/password/change');

// ユーザー新規登録
Route::controller(UserRegisterController::class)->group(function () {
    Route::get('/user_register', 'index')->name('user_register');
    Route::post('/user_register', 'store')->name('user_store');
});


// ==========================================
//  ログイン必須エリア
// ==========================================
Route::middleware('auth')->group(function () {

    //  ホーム画面（Dashboard）
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    //  ユーザー管理
    // Route::controller(UserRegisterController::class)->group(function () {
    // Route::get('/user_list', 'list')->name('user_list');
    // Route::get('/user_edit/{id}', 'edit');
    // Route::put('/user_update/{id}', 'update')->name('user_update');
    // });

    //  食材管理（Kitchen Stock）
    Route::controller(IngredientController::class)->group(function () {
        Route::get('/ingredient_list', 'index')->name('ingredient.list');
        Route::get('/ingredient_register', 'create')->name('ingredient.register');
        Route::post('/ingredients', 'store');
        Route::delete('/ingredient_delete/{id}', 'destroy');
        Route::get('/ingredient_update/{id}', 'edit');
        Route::put('/ingredient_update/{id}', 'update');
    });

    //  レシピ管理（MYRecipe Book）
    Route::controller(RecipeController::class)->group(function () {
        Route::get('/recipe_list', 'index')->name('recipe.list');
        Route::get('/recipe_detail/{id}', 'show')->name('recipe.detail');

        // 編集・更新・削除
        Route::get('/recipe_update/{id}', 'edit')->name('recipe.edit');
        Route::patch('/recipe_update/{id}', 'update')->name('recipe.update');
        Route::delete('/recipe_delete/{id}', 'destroy')->name('recipe.destroy'); // 重複していた削除もこれ1つに！

        // お気に入り機能（Ajax）
        Route::post('/recipe/{recipe}/favorite', 'toggleFavorite')->name('recipes.favorite.toggle');

        // AI献立提案用のルート
        Route::get('/recipe/suggest', 'suggest')->name('recipe.suggest');
        Route::post('/recipe/suggest/save', 'saveSuggestion')->name('recipe.suggest.save');

        // レシピを作った後の「在庫消費ギミック」用のルート
        Route::post('/recipe/{recipe}/consume', 'consumeIngredients')->name('recipe.consume');
    });
});
