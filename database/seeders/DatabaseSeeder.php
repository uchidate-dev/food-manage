<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Recipe;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //  カテゴリーを作成
        $this->call([
            AdminUserSeeder::class,
            RecipeCategorySeeder::class,
            IngredientCategorySeeder::class,
        ]);

        // ===================================== del s
        // //一般ユーザー 5人（id=2〜6になる想定）
        // User::factory()->count(5)->create([
        //     'role' => 0,
        // ]);

        // //  レシピを作成
        // $this->call([
        //     RecipeSeeder::class,
        // ]);
        // ===================================== del e
    }
}
