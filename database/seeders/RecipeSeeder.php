<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $guest = User::where('email', 'guest@example.com')->first();

        if (!$guest) {
            return;
        }

        // 封印を解いて、旧チームのゴミデータを綺麗にお掃除！🧹
        Recipe::where('user_id', $guest->id)->delete();

        // 🌟 面接官を魅了する、最強の3種レシピ！
        $recipes = [
            [
                'title' => '彩りバッチリ！卵とブロッコリーのサラダ',
                'cat' => 1, // カテゴリID（環境に合わせてね）
                'memo' => "大人用には、最後に粒マスタードを小さじ1混ぜると最高のおつまみになります！\nお弁当の隙間埋めにも大活躍の万能おかずです。",
                'kids_tips' => "マヨネーズを少し多めにすると、ブロッコリーの苦味が消えて子供もパクパク食べてくれます！",
                'steps' => [
                    "ブロッコリーは小さめの房に分け、耐熱ボウルに入れてふんわりラップをし、レンジ(600W)で約1分半加熱する。",
                    "ゆで卵を作り、殻を剥いて粗く刻む。",
                    "ボウルにブロッコリーとゆで卵を入れ、マヨネーズと少々の塩で和えれば完成！"
                ],
                'nutrition' => ["タンパク質" => 4, "ビタミン" => 5, "カルシウム" => 3, "鉄分" => 2, "エネルギー" => 3],
                'ingredients' => [
                    ['name' => '卵', 'qty' => '2個'],
                    ['name' => '冷凍ブロッコリー', 'qty' => '半房'],
                    ['name' => 'マヨネーズ', 'qty' => '大さじ2']
                ]
            ],
            [
                'title' => '10分で完成！豚バラとキャベツのガリバタ醤油',
                'cat' => 1,
                'memo' => "忙しい日の救世主！ご飯が無限に進む危険なおかずです🤤\nキャベツは手でちぎれば包丁いらず！",
                'kids_tips' => "少しお砂糖を足して甘辛にすると、お肉も野菜もモリモリ食べてくれます！",
                'steps' => [
                    "豚バラ肉は食べやすい大きさに、キャベツはざく切り（または手でちぎる）にする。",
                    "フライパンにごま油を熱し、豚肉を炒める。色が変わったらキャベツを加えてしんなりするまで炒める。",
                    "バター、醤油、すりおろしニンニクを加えてサッと炒め合わせたら完成！"
                ],
                'nutrition' => ["タンパク質" => 4, "ビタミン" => 3, "カルシウム" => 1, "鉄分" => 2, "エネルギー" => 5],
                'ingredients' => [
                    ['name' => '豚バラ肉', 'qty' => '200g'],
                    ['name' => 'キャベツ', 'qty' => '1/4玉'],
                    ['name' => 'バター', 'qty' => '10g'],
                    ['name' => '醤油', 'qty' => '大さじ1']
                ]
            ],
            [
                'title' => '幼稚園お弁当の定番！ちくわとタコさん',
                'cat' => 1,
                'memo' => "ウィンナーとちくわの消費に！\n黒ごまでちくわ鳥の目をつけると一気に可愛くなります。",
                'kids_tips' => "ピックにお気に入りのキャラクターを使うと、テンション爆上がり間違いなし✨",
                'steps' => [
                    "ウィンナーは半分に切り、足になるように十字に切り込みを入れる。",
                    "ちくわは3等分に切り、枝豆をくちばしに見立てて差し込む。",
                    "フライパンでウィンナーを炒め、足が開いたら完成！ちくわ鳥と一緒にお弁当箱へ♪"
                ],
                'nutrition' => ["タンパク質" => 3, "ビタミン" => 1, "カルシウム" => 2, "鉄分" => 1, "エネルギー" => 3],
                'ingredients' => [
                    ['name' => 'ウィンナー', 'qty' => '3本'],
                    ['name' => 'ちくわ', 'qty' => '1本'],
                    ['name' => '冷凍枝豆', 'qty' => '数粒']
                ]
            ]
        ];

        // ぐるぐる回して一気に登録！
        foreach ($recipes as $data) {
            $recipe = Recipe::create([
                'user_id' => $guest->id,
                'recipe_category_id' => $data['cat'],
                'title' => $data['title'],
                'memo' => $data['memo'],
                'kids_tips' => $data['kids_tips'],
                'steps' => $data['steps'],
                'nutrition' => $data['nutrition'],
            ]);

            foreach ($data['ingredients'] as $ingData) {
                // 食材マスタになければ作る（一応）
                $ing = \App\Models\Ingredient::firstOrCreate(
                    ['name' => $ingData['name'], 'user_id' => $guest->id],
                    ['ingredient_cat_id' => 9, 'storage_type' => 0, 'quantity' => 0, 'delete_flg' => 0]
                );

                // 中間テーブルに登録
                DB::table('recipe_ingredients')->insert([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ing->id,
                    'quantity' => $ingData['qty'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
