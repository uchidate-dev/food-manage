<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use Carbon\Carbon; // 日付の計算

class IngredientController extends Controller
{
    // ログイン必須の設定
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 食材一覧
    public function index(Request $request)
    {
        // ログインユーザーのIDを取得
        $userId = auth()->id();

        // 画面から送られてきた「絞り込み」と「並び替え」の条件を受け取る
        $categoryId = $request->input('category_id');
        // デフォルトは「賞味期限が近い順」
        $sort = $request->input('sort', 'expiry_asc');

        // クエリ（DBへの命令）のベース作成
        $query = Ingredient::with('category')
            ->where('user_id', $userId)
            ->where('delete_flg', 0);

        // カテゴリーで絞り込む（選択された場合）
        if (!empty($categoryId)) {
            $query->where('ingredient_cat_id', $categoryId);
        }

        // 選択された順番で並び替える
        if ($sort === 'created_desc') {
            // 新しく登録した順
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'category_asc') {
            // カテゴリー順
            $query->orderBy('ingredient_cat_id', 'asc')
                // 同じカテゴリーの中でも「期限なし」は一番下に表示
                ->orderByRaw('CASE WHEN expiration_date IS NULL THEN 1 ELSE 0 END')
                ->orderBy('expiration_date', 'asc');
        } else {
            // デフォルト：賞味期限が近い順
            // 日付が空（NULL）のものは一番最後にする
            $query->orderByRaw('CASE WHEN expiration_date IS NULL THEN 1 ELSE 0 END')
                ->orderBy('expiration_date', 'asc');
        }

        // 条件を全てくっつけてデータを取得
        $ingredientsData = $query->get();

        // ★アイコンの変換辞書（さっき増やしたカテゴリーに合わせて増強！！）
        $iconMap = [
            1 => '🥦', // 野菜
            2 => '🥩', // 肉
            3 => '🐟', // 魚介・水産加工品
            4 => '🥚', // 卵・乳製品 (舞子さんファインプレー！)
            5 => '🧈', // 大豆製品 (豆腐っぽいのがないのでバター等で代用)
            6 => '🍜', // 麺類
            7 => '🍚', // 米・粉類
            8 => '❄️', // 冷凍食品
            9 => '🧺', // その他
        ];

        // 表示用データ（アイコンや期限）を追加
        $ingredients = $ingredientsData->map(function ($item) use ($iconMap) {

            // アイコンを設定(バグ防止の保険)
            $icon = $iconMap[$item->ingredient_cat_id] ?? '🧺';

            // 賞味期限の計算 (Carbonを使って「今日」と比較)
            $statusColor = 'gray';
            $statusText = '期限なし';

            if ($item->expiration_date) {
                $expireDate = Carbon::parse($item->expiration_date)->startOfDay();
                $today = Carbon::today();
                $diffDays = $today->diffInDays($expireDate, false); // 過去ならマイナスになる

                if ($diffDays < 0) {
                    $statusColor = 'red';
                    $statusText = '期限切れ (' . $diffDays . '日)';
                } elseif ($diffDays <= 3) {
                    $statusColor = 'amber';
                    $statusText = '⚠️ あと' . $diffDays . '日';
                } else {
                    $statusColor = 'gray';
                    $statusText = 'あと ' . $diffDays . '日';
                }
            }

            // Viewにデータを渡す
            return [
                'id' => $item->id,
                'ingredient_cat_id' => $item->ingredient_cat_id,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'expiration_date' => $item->expiration_date,
                'storage_type' => $item->storage_type,
                'delete_flg' => $item->delete_flg,
                'memo' => $item->memo,

                // 表示用データ
                'category_name' => $item->category ? $item->category->name : '未分類',
                'icon' => $icon,
                'status_color' => $statusColor,
                'status_text' => $statusText,
            ];
        });

        // 食材一覧画面にデータと検索条件を渡して表示
        return view('ingredient.ingredient_list', [
            'ingredients' => $ingredients,
            'selectedCategory' => $categoryId, //画面側で選択状態をキープ
            'selectedSort' => $sort //画面側で選択状態をキープ
        ]);
    }

    // 食材新規登録画面の表示
    public function create()
    {
        // 1. DBから食材カテゴリーを全件持ってくる
        $categories = IngredientCategory::all();

        // 2. 画面（Blade）にカテゴリー一覧を渡す
        return view('ingredient.ingredient_register', [
            'categories' => $categories
        ]);
    }

    // 食材新規登録の保存処理
    public function store(Request $request)
    {
        // 画面から送られてきたデータをDBに保存する準備
        $ingredient = new Ingredient();

        // ログインユーザーのIDをセット
        $ingredient->user_id = auth()->id();

        // 画面の入力内容をセット
        $ingredient->ingredient_cat_id = $request->ingredient_cat_id;
        $ingredient->name = $request->name;
        $ingredient->storage_type = $request->storage_type;
        $ingredient->quantity = $request->quantity;
        $ingredient->expiration_date = $request->expiration_date;
        $ingredient->memo = $request->memo;
        $ingredient->delete_flg = 0;

        // データベースに保存
        $ingredient->save();

        // 押されたボタンによって遷移先を変える
        if ($request->action === 'return') {
            // 「登録して一覧へ」ボタンが押された場合
            return redirect('/ingredient_list')->with('success', '食材を登録しました');
        } else {
            // 「続けて登録する」ボタンが押された場合
            return redirect('/ingredient_register')->with('success', '食材を登録しました。続けて入力できます');
        }
    }

    // 食材の論理削除（delete_flgを1にする処理）
    // ★ Requestを受け取れるように (Request $request, $id) に変更
    public function destroy(\Illuminate\Http\Request $request, $id)
    {
        // ログインしているユーザーの食材を探す
        $ingredient = Ingredient::where('user_id', auth()->id())->find($id);

        // 食材が見つかったら、delete_flgを1（非表示）に変更して保存
        if ($ingredient) {
            $ingredient->delete_flg = 1;
            $ingredient->save();
        }

        // もし「Ajax（裏側からの通信）」でお願いされたら、画面移動させずに「成功したよ！」って返事だけする
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => '食材を削除しました。']);
        }

        // 一覧画面に戻って、トースト通知を出す
        return redirect('/ingredient_list')->with('success', '食材を削除しました。');
    }

    // 食材の編集画面を表示する処理
    public function edit($id)
    {
        // ログインしているユーザーの食材を探す
        $ingredient = Ingredient::where('user_id', auth()->id())->find($id);

        // 他人のデータを見ようとした場合は一覧に弾く（念のため）
        if (!$ingredient) {
            return redirect('/ingredient_list')->with('error', '不正なアクセスです。');
        }

        //  DBから食材カテゴリーを全券持ってくる
        $categories = IngredientCategory::all();

        // 編集画面にデータを渡して表示する
        return view('ingredient.ingredient_update', [
            'ingredient' => $ingredient,
            'categories' => $categories
        ]);
    }

    // 食材のデータを上書き保存する処理
    public function update(Request $request, $id)
    {
        // ログインしているユーザーの食材を探す
        $ingredient = Ingredient::where('user_id', auth()->id())->find($id);

        // 他人のデータを更新しようとした場合は一覧に弾く
        if (!$ingredient) {
            return redirect('/ingredient_list')->with('error', '不正なアクセスです。');
        }

        // 2. フォームから送られてきた新しいデータで上書きする
        $ingredient->ingredient_cat_id = $request->ingredient_cat_id;
        $ingredient->name = $request->name;
        $ingredient->storage_type = $request->storage_type;
        $ingredient->quantity = $request->quantity;
        $ingredient->expiration_date = $request->expiration_date;
        $ingredient->memo = $request->memo;

        // 3. データベースに保存（更新）
        $ingredient->save();

        // 4. 一覧画面に戻って、トースト通知を出す
        return redirect('/ingredient_list')->with('success', '食材の情報を更新しました');
    }
}
