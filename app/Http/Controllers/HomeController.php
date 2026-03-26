<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $me = auth()->user();

        $today  = Carbon::today();

        $d0 = $today->copy();
        $d1 = $today->copy()->addDay();
        $d2 = $today->copy()->addDays(2);
        $d3 = $today->copy()->addDays(3);

        // ★表示対象ユーザー（role=0は自分固定 / role=1はuidで切替）
        $selectedUserId = $me->id;
        $usersForFilter = collect();

        if ((int)$me->role === 1) {
            $uid = (int) $request->query('uid', $me->id);

            if (User::whereKey($uid)->exists()) {
                $selectedUserId = $uid;
            }

            $usersForFilter = User::orderBy('name')->get(['id', 'name']);
        }

        // 共通：表示対象（論理削除なし） + ★user_idで絞る
        $base = Ingredient::query()
            ->where('delete_flg', 0)
            ->where('user_id', $selectedUserId);

        // 日付が「有効」なもの（null除外 + 0000-00-00除外）
        $withValidDate = (clone $base)
            ->whereNotNull('expiration_date');

        // 日付が「未設定」扱い（null or 0000-00-00）
        $unsetQuery = (clone $base)
            ->whereNull('expiration_date');

        $unsetCount = (clone $unsetQuery)->count();

        $unset5 = (clone $unsetQuery)
            ->orderBy('name')
            ->limit(5)
            ->get();

        $unsetAll = collect();
        if ($unsetCount > 5) {
            $unsetAll = (clone $unsetQuery)
                ->orderBy('name')
                ->get();
        }

        // --- 食材期限カレンダー ---
        $expired = (clone $withValidDate)
            ->where('expiration_date', '<', $d0->toDateString())
            ->orderBy('expiration_date')
            ->orderBy('name')
            ->get()
            ->map(function ($row) use ($today) {
                $days = Carbon::parse($row->expiration_date)->diffInDays($today);
                $row->expire_label = "{$row->name} -{$days}d";
                return $row;
            });

        $calToday = (clone $withValidDate)->whereDate('expiration_date', $d0)->orderBy('name')->get();
        $calD1    = (clone $withValidDate)->whereDate('expiration_date', $d1)->orderBy('name')->get();
        $calD2    = (clone $withValidDate)->whereDate('expiration_date', $d2)->orderBy('name')->get();
        $calD3    = (clone $withValidDate)->whereDate('expiration_date', $d3)->orderBy('name')->get();

        $within3 = collect()->merge($calD1)->merge($calD2)->merge($calD3);

        // --- 今ある食材一覧 ---
        $allSorted = (clone $base)
            ->orderByRaw("CASE
                WHEN expiration_date IS NULL THEN 1
                ELSE 0
            END")
            ->orderBy('expiration_date')
            ->orderBy('name')
            ->get();

        $fridge = [
            'veg'    => $allSorted->where('storage_type', 0)->where('ingredient_cat_id', 1)->values(),
            'meat'   => $allSorted->where('storage_type', 0)->where('ingredient_cat_id', 2)->values(),
            'fish'   => $allSorted->where('storage_type', 0)->whereIn('ingredient_cat_id', [3, 4])->values(),
            'noodle' => $allSorted->where('storage_type', 0)->whereIn('ingredient_cat_id', [5, 6])->values(),
            'other'  => $allSorted->where('storage_type', 0)->where('ingredient_cat_id', 7)->values(),
        ];

        $freezer = $allSorted->where('storage_type', 1)->values();

        $statusCounts = [
            'expired'  => $expired->count(),
            'today'    => $calToday->count(),
            'within3'  => $within3->count(),
            'unset'    => $unsetCount,
            'freezer'  => $freezer->count(),
        ];

        $expiredList = $expired;
        $todayList   = $calToday;
        $hasAlertItems = ($expiredList->count() > 0 || $todayList->count() > 0);

        $openModal = session()->pull('open_home_modal', false);
        $showExpiredModal = $openModal;

        // ✅ --- 最近見たレシピ（最新順 / 同じrecipe_idは1回だけ / 10件） ---
        $latestViewsSub = DB::table('recipe_views')
            ->select('recipe_id', DB::raw('MAX(viewed_at) as last_viewed'))
            ->where('user_id', $selectedUserId)
            ->groupBy('recipe_id');

        $recentRecipes = DB::table('recipes')
            ->joinSub($latestViewsSub, 'rv', function ($join) {
                $join->on('recipes.id', '=', 'rv.recipe_id');
            })
            ->orderByDesc('rv.last_viewed')
            ->limit(10)
            ->get(['recipes.id', 'recipes.title', 'rv.last_viewed']);

        // ✅ --- お気に入りレシピ（最新10件） ---
        $favoriteRecipes = DB::table('recipe_favorites as rf')
            ->join('recipes as r', 'r.id', '=', 'rf.recipe_id')
            ->where('rf.user_id', $selectedUserId)
            ->orderByDesc('rf.created_at')   // created_atが無いなら ->orderByDesc('rf.id') に変更
            ->limit(10)
            ->get([
                'r.id',
                'r.title',
                'rf.created_at as favorited_at',
            ]);
// ✅ --- おすすめレシピ（在庫寄せ） ---
// 期限の近さ順（期限切れ→今日→3日以内→それ以外→未設定）で食材名を並べ、
// 上から順に recipes.ingredients にヒットするワードを探す。
// ヒットした時だけ：検索条件＜...＞ と レシピ一覧（最大10件）を表示。

$recommendTags      = collect(); // 表示用：＜食材名＞
$recommendedRecipes = collect(); // 表示用：おすすめレシピ（最大10件）

// ① 候補ワード（食材名）を「期限の近さ順」で取得（上位8件まで）
$candidates = DB::table('ingredients')
    ->where('delete_flg', 0)
    ->where('user_id', $selectedUserId)
    ->where('storage_type', 0)
    ->orderByRaw("
        CASE
            WHEN expiration_date IS NULL THEN 9
            WHEN expiration_date < CURDATE() THEN 0
            WHEN expiration_date = CURDATE() THEN 1
            WHEN expiration_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY) THEN 2
            ELSE 3
        END
    ")
    ->orderBy('expiration_date')
    ->orderBy('name')
    ->pluck('name')
    ->map(fn ($s) => trim((string) $s))
    ->filter(fn ($s) => $s !== '')
    ->unique()
    ->take(8)
    ->values();

// ② 上から順に「最初にヒットしたワード」を採用
foreach ($candidates as $word) {
    $hit = DB::table('recipes')
        ->where('private_flg', 0)         
        ->whereNotNull('ingredients')
        ->where('ingredients', 'like', "%{$word}%")
        ->limit(10)
        ->get(['id', 'title']);

    if ($hit->isNotEmpty()) {
        $recommendTags      = collect([$word]); 
        $recommendedRecipes = $hit;            
        break;
    }
}

        return view('home', [
            'today' => $today,
            'd0' => $d0,
            'd1' => $d1,
            'd2' => $d2,
            'd3' => $d3,

            'expired' => $expired,
            'calToday' => $calToday,
            'calD1' => $calD1,
            'calD2' => $calD2,
            'calD3' => $calD3,

            'fridge' => $fridge,
            'freezer' => $freezer,

            'unsetCount' => $unsetCount,
            'unset5'     => $unset5,
            'unsetAll'   => $unsetAll,

            'statusCounts' => $statusCounts,

            'hasAlertItems' => $hasAlertItems,
            'showExpiredModal' => $showExpiredModal,
            'expiredList' => $expiredList,
            'todayList' => $todayList,
            'openModal' => $openModal,

            'selectedUserId' => $selectedUserId,
            'usersForFilter' => $usersForFilter,

            'recentRecipes' => $recentRecipes,
            'favoriteRecipes' => $favoriteRecipes,
            'recommendedRecipes' => $recommendedRecipes,
            'recommendTags' => $recommendTags,
        ]);
    }

}
