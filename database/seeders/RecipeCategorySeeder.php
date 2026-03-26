<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RecipeCategory;

class RecipeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            '和食',
            '洋食',
            '中華',
            'スイーツ',
            'その他',
        ];

        foreach ($categories as $name) {
            RecipeCategory::create([
                'name' => $name,
            ]);
        }
    }
}
