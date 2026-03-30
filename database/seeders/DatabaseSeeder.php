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
            UserSeeder::class,
            RecipeCategorySeeder::class,
            IngredientCategorySeeder::class,
            IngredientSeeder::class,
            RecipeSeeder::class,

        ]);
    }
}
