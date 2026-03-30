<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Models\User;
use Carbon\Carbon;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        // デモユーザーを探す
        $guest = User::where('email', 'guest@example.com')->first();

        if (!$guest) {
            return;
        }

        Ingredient::where('user_id', $guest->id)->delete();

        $today = Carbon::today();

        $ingredients = [
            // 【緊急：赤色エリア】冷蔵（0）
            ['name' => 'ウィンナー', 'cat' => 2, 'qty' => 1, 'storage' => 0, 'exp' => $today->copy()->subDay()->toDateString()], // 昨日
            ['name' => '豚バラ肉', 'cat' => 2, 'qty' => 1, 'storage' => 0, 'exp' => $today->toDateString()], // 今日

            // 【そろそろ：ベージュエリア】冷蔵（0）
            ['name' => 'ちくわ', 'cat' => 3, 'qty' => 1, 'storage' => 0, 'exp' => $today->copy()->addDays(2)->toDateString()],
            ['name' => '卵', 'cat' => 4, 'qty' => 4, 'storage' => 0, 'exp' => $today->copy()->addDays(3)->toDateString()],

            //  【冷凍庫（1）】
            ['name' => '冷凍ブロッコリー', 'cat' => 1, 'qty' => 1, 'storage' => 1, 'exp' => $today->copy()->addMonths(1)->toDateString()],
            ['name' => '冷凍枝豆', 'cat' => 1, 'qty' => 1, 'storage' => 1, 'exp' => $today->copy()->addMonths(2)->toDateString()],
            ['name' => '鮭の切り身', 'cat' => 3, 'qty' => 2, 'storage' => 1, 'exp' => $today->copy()->addWeeks(2)->toDateString()],

            // 【ストック：期限なし】
            ['name' => 'キャベツ', 'cat' => 1, 'qty' => 1, 'storage' => 0, 'exp' => null],
            ['name' => '玉ねぎ', 'cat' => 1, 'qty' => 2, 'storage' => 0, 'exp' => null],
        ];

        // データをデータベースに
        foreach ($ingredients as $ing) {
            Ingredient::create([
                'user_id'           => $guest->id,
                'ingredient_cat_id' => $ing['cat'],
                'name'              => $ing['name'],
                'storage_type'      => $ing['storage'], // 冷蔵(0)・冷凍(1)
                'quantity'          => $ing['qty'],
                'expiration_date'   => $ing['exp'],
                'delete_flg'        => 0,
            ]);
        }
    }
}
