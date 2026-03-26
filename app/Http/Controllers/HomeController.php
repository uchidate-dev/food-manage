<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $me = Auth::user();
        $today = Carbon::today();

        // --- 1. ユーザーの有効な食材（冷蔵庫・冷凍庫すべて）を取得 ---
        $baseQuery = Ingredient::where('user_id', $me->id)
            ->where('delete_flg', 0);

        // --- 2. 期限切れ食材（優先度MAX：赤色） ---
        $expired = clone $baseQuery;
        $expiredItems = $expired->whereNotNull('expiration_date')
            ->where('expiration_date', '<', $today->toDateString())
            ->orderBy('expiration_date', 'asc')
            ->get();

        // --- 3. 今日・明日期限の食材（優先度高：黄色） ---
        $soon = clone $baseQuery;
        $soonItems = $soon->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [
                $today->toDateString(),
                $today->copy()->addDays(1)->toDateString() // 今日と明日
            ])
            ->orderBy('expiration_date', 'asc')
            ->get();

        // --- 4. お気に入りレシピ（Viewに渡す用 / 後で拡張） ---
        // ※今はエラー防止のために空配列を渡しておく
        $favoriteRecipes = [];

        // --- 5. 画面（Blade）にデータを渡す ---
        return view('home', compact(
            'expiredItems',
            'soonItems',
            'favoriteRecipes'
        ));
    }
}
