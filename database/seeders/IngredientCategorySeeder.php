<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IngredientCategory;

class IngredientCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            '野菜',
            '肉',
            '魚',
            '貝類',
            '麺類',
            '米',
            'その他',
        ];

        foreach ($categories as $name) {
            IngredientCategory::create([
                'name' => $name,
            ]);
        }
    }
}
