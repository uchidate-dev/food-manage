<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // 1x1 PNG (超軽量) のbase64
        $dummyPngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=';

        $now = now();

        $recipes = [
            [
                'user_id' => rand(2, 4),
                'recipe_category_id' => rand(1, 3),
                'title' => '鶏もも肉のさっぱり炒め',
                'url' => 'https://example.com/recipe1',
                'private_flg' => 0,
                'favorite_flg' => 1,
                'ingredients' => "鶏もも肉 300g\n玉ねぎ 1/2個\nポン酢 大さじ3\nサラダ油 小さじ2\n塩 少々\nこしょう 少々",
                'finished_image' => $dummyPngBase64,
                'memo' => "玉ねぎは薄切り。\n仕上げにポン酢を入れたら加熱しすぎない。",
                'steps' => [
                    '玉ねぎを薄切りにする。',
                    '鶏もも肉を一口大に切り、塩こしょうをする。',
                    'フライパンに油を熱し、鶏肉を皮目から焼く。',
                    '両面に焼き色が付いたら玉ねぎを加えて炒める。',
                    '火を弱めてポン酢を回しかけ、全体を絡めて止める。',
                ],
            ],
            [
                'user_id' => rand(2, 4),
                'recipe_category_id' => rand(1, 3),
                'title' => '豚こまの甘辛丼',
                'url' => 'https://example.com/recipe2',
                'private_flg' => 0,
                'favorite_flg' => 0,
                'ingredients' => "豚こま肉 250g\n玉ねぎ 1/2個\n醤油 大さじ2\nみりん 大さじ2\n砂糖 小さじ2\nごはん 適量",
                'finished_image' => $dummyPngBase64,
                'memo' => "先に玉ねぎをしんなりさせると時短。",
                'steps' => [
                    '玉ねぎを薄切りにする。',
                    'フライパンで玉ねぎを炒める。',
                    '豚こま肉を加えて色が変わるまで炒める。',
                    '醤油・みりん・砂糖を入れて煮絡める。',
                    'ごはんに盛り、上にのせる。',
                ],
            ],
            [
                'user_id' => rand(2, 4),
                'recipe_category_id' => rand(1, 3),
                'title' => '豆腐とわかめの味噌汁',
                'url' => null,
                'private_flg' => 0,
                'favorite_flg' => 1,
                'ingredients' => "水 400ml\nだし 小さじ2（顆粒）\n豆腐 150g\n乾燥わかめ 小さじ2\n味噌 大さじ2",
                'finished_image' => $dummyPngBase64,
                'memo' => "味噌は火を止めてから溶く。",
                'steps' => [
                    '鍋に水とだしを入れて沸かす。',
                    '豆腐を食べやすく切って入れる。',
                    'わかめを入れる。',
                    '火を止めて味噌を溶く。',
                ],
            ],
            [
                'user_id' => rand(2, 4),
                'recipe_category_id' => rand(1, 3),
                'title' => 'トマトと卵の中華スープ',
                'url' => null,
                'private_flg' => 0,
                'favorite_flg' => 0,
                'ingredients' => "トマト 1個\n卵 1個\n水 400ml\n鶏がらスープの素 小さじ2\n塩 少々\nこしょう 少々",
                'finished_image' => $dummyPngBase64,
                'memo' => "卵は溶いて、沸騰したところに細く流す。",
                'steps' => [
                    'トマトを一口大に切る。',
                    '鍋に水と鶏がらスープの素を入れて沸かす。',
                    'トマトを入れて1〜2分煮る。',
                    '溶き卵を回し入れ、ふわっと固める。',
                    '塩こしょうで味を整える。',
                ],
            ],
            [
                'user_id' => rand(2, 4),
                'recipe_category_id' => rand(1, 3),
                'title' => 'きのこの和風パスタ',
                'url' => null,
                'private_flg' => 0,
                'favorite_flg' => 0,
                'ingredients' => "スパゲッティ 200g\nしめじ 1/2株\nえのき 1/2袋\nバター 10g\n醤油 大さじ1\n塩 少々\nこしょう 少々",
                'finished_image' => $dummyPngBase64,
                'memo' => "茹で汁を少し入れて乳化させると絡む。",
                'steps' => [
                    'パスタを表示時間どおりに茹でる。',
                    'きのこをほぐして切る。',
                    'フライパンでバターを溶かし、きのこを炒める。',
                    '茹でたパスタと茹で汁少量を入れて混ぜる。',
                    '醤油で香り付けし、塩こしょうで調整する。',
                ],
            ],
        ];

        // さらに件数を増やしたい場合：上の5件を複製してタイトルだけ変える等も可能。
        // 今回は10件にする（5件×2）例
        $recipes = array_merge($recipes, $this->duplicateWithSuffix($recipes, '（別案）'));

        foreach ($recipes as $r) {
            $steps = $r['steps'] ?? [];

            DB::table('recipes')->insert([
                'user_id' => rand(2, 4),
                'recipe_category_id' => rand(1, 3),

                'title' => $r['title'],
                'url' => $r['url'],
                'private_flg' => $r['private_flg'],
                'favorite_flg' => $r['favorite_flg'],

                'ingredients' => $r['ingredients'],
                'finished_image' => $r['finished_image'],
                'memo' => $r['memo'],

                // step1..step15
                'step1' => $steps[0] ?? null,
                'step2' => $steps[1] ?? null,
                'step3' => $steps[2] ?? null,
                'step4' => $steps[3] ?? null,
                'step5' => $steps[4] ?? null,
                'step6' => $steps[5] ?? null,
                'step7' => $steps[6] ?? null,
                'step8' => $steps[7] ?? null,
                'step9' => $steps[8] ?? null,
                'step10' => $steps[9] ?? null,
                'step11' => $steps[10] ?? null,
                'step12' => $steps[11] ?? null,
                'step13' => $steps[12] ?? null,
                'step14' => $steps[13] ?? null,
                'step15' => $steps[14] ?? null,

                // step_image1..step_image15（今回は全部nullでもOK。例として1枚だけ入れる）
                'step_image1' => $dummyPngBase64,
                'step_image2' => null,
                'step_image3' => null,
                'step_image4' => null,
                'step_image5' => null,
                'step_image6' => null,
                'step_image7' => null,
                'step_image8' => null,
                'step_image9' => null,
                'step_image10' => null,
                'step_image11' => null,
                'step_image12' => null,
                'step_image13' => null,
                'step_image14' => null,
                'step_image15' => null,

                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function duplicateWithSuffix(array $recipes, string $suffix): array
    {
        $copied = [];
        foreach ($recipes as $r) {
            $r2 = $r;
            $r2['title'] = $r['title'] . $suffix;
            $r2['favorite_flg'] = 0; // 複製分は通常にしておく
            $copied[] = $r2;
        }
        return $copied;
    }
}
