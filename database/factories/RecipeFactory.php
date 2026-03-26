<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeFactory extends Factory
{
    public function definition(): array
    {
        $ingredients = [
            '玉ねぎ','にんじん','じゃがいも','鶏もも肉','豚こま','牛こま','卵','豆腐','キャベツ','もやし',
            'ねぎ','にんにく','しょうが','トマト','きゅうり','ほうれん草','しめじ','えのき','ツナ缶','ベーコン',
            '米','パスタ','うどん','味噌','しょうゆ','みりん','酒','砂糖','塩','こしょう','ごま油','オリーブオイル'
        ];

        $titleHeads = ['簡単','時短','定番','節約','さっぱり','こってり','お弁当','作り置き','家族向け','野菜たっぷり'];
        $titleMains = ['カレー','炒め','煮込み','スープ','丼','パスタ','うどん','サラダ','グラタン','オムレツ'];
        $titleTails = ['風','レシピ','ごはん','おかず','メニュー'];

        $picked = $this->faker->randomElements($ingredients, 3);
        $title = $this->faker->randomElement($titleHeads)
               . $this->faker->randomElement($titleMains)
               . $this->faker->randomElement($titleTails);

        return [
            'user_id' => $this->faker->numberBetween(2, 6),
            'recipe_category_id' => $this->faker->numberBetween(1, 5),
            'title' => $title,
            'url' => null,
            'private_flg' => 0,
            'favorite_flg' => 0,
            'ingredients' => implode('、', $picked),
            'finished_image' => null,
            'memo' => 'メモ：' . $this->faker->numberBetween(1, 999),
            'step1' => '材料を切る',
            'step2' => '加熱して味を整える',
        ];
    }
}