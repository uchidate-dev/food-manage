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
            '魚介・水産加工品',
            '卵・乳製品',
            '大豆製品',
            '麺類',
            '米・粉類',
            '冷凍食品',
            'その他',
        ];

        foreach ($categories as $name) {
            IngredientCategory::create([
                'name' => $name,
            ]);
        }
    }
}
