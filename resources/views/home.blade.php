@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
@php
// 曜日
$wk = ['日','月','火','水','木','金','土'];

// 上段ステータス
$sc = $statusCounts ?? ['expired'=>0,'today'=>0,'within3'=>0,'unset'=>0,'freezer'=>0];
$statusPills = [
['key'=>'expired', 'icon'=>'⛔', 'label'=>'期限切れ', 'class'=>'nt-pill nt-pill--danger'],
['key'=>'today', 'icon'=>'📅', 'label'=>'今日', 'class'=>'nt-pill nt-pill--today'],
['key'=>'within3', 'icon'=>'⚠️', 'label'=>'3日以内', 'class'=>'nt-pill nt-pill--warn'],
['key'=>'unset', 'icon'=>'❓', 'label'=>'期限未設定', 'class'=>'nt-pill nt-pill--mute'],
['key'=>'freezer', 'icon'=>'❄️', 'label'=>'冷凍', 'class'=>'nt-pill nt-pill--ice'],
];

// カレンダー列
$calCols = [
['key'=>'expired','label'=>'期限切れ','class'=>'h-expired','icon'=>'bi-emoji-dizzy-fill','risk'=>'expired'],
['key'=>'today', 'label'=>'今日', 'class'=>'h-today', 'icon'=>'bi-emoji-tear-fill', 'risk'=>'soon'],
['key'=>'d1', 'label'=>$d1->format('n/j').'（'.$wk[$d1->dayOfWeek].'）','class'=>'h-day','icon'=>'bi-emoji-tear-fill','risk'=>'soon'],
['key'=>'d2', 'label'=>$d2->format('n/j').'（'.$wk[$d2->dayOfWeek].'）','class'=>'h-day','icon'=>'bi-emoji-tear-fill','risk'=>'soon'],
['key'=>'d3', 'label'=>$d3->format('n/j').'（'.$wk[$d3->dayOfWeek].'）','class'=>'h-last','icon'=>'bi-emoji-smile-fill','risk'=>'safe'],
];

// カレンダー中身
$calItems = [
'expired' => $expired->map(fn($ing)=> ['t'=>$ing->expire_label,'red'=>true])->all(),
'today' => $calToday->map(fn($ing)=> ['t'=>$ing->name,'red'=>true])->all(),
'd1' => $calD1->map(fn($ing)=> ['t'=>$ing->name,'red'=>false])->all(),
'd2' => $calD2->map(fn($ing)=> ['t'=>$ing->name,'red'=>false])->all(),
'd3' => $calD3->map(fn($ing)=> ['t'=>$ing->name,'red'=>false])->all(),
];
@endphp

{{-- =========================
  上段：ステータス
========================= --}}
<div class="nt-topbar">
    <div class="nt-topbar-inner">
        <div class="nt-status nt-status--tall">
            @foreach($statusPills as $p)
            <div class="{{ $p['class'] }}">
                <div class="nt-pill-top">
                    <span class="nt-pill-ico">{{ $p['icon'] }}</span>
                    <span class="nt-pill-label">{{ $p['label'] }}</span>
                </div>
                <div class="nt-pill-bottom">
                    <span class="nt-pill-num">{{ $sc[$p['key']] ?? 0 }}</span>
                    <span class="nt-pill-unit">件</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="nt-wrap">

    {{-- =========================
    中段：食材期限カレンダー（全幅） + 右上ボタン
  ========================= --}}
    <div class="nt-area nt-area--cal">
        <div class="nt-block">

            <div class="nt-block-head">
                <div class="nt-title nt-title--bracket mb-0">食材期限カレンダー</div>

               <a href="{{ url('/ingredient_list') }}" class="btn nt-btn-orange nt-btn-sm">食材一覧</a>
            </div>

            <table class="nt-table">
                <thead>
                    <tr>
                        @foreach($calCols as $c)
                        <th class="nt-head {{ $c['class'] }}">
                            <span class="nt-head-inner">
                                <span class="nt-ico nt-ico-{{ $c['risk'] }}">
                                    <i class="bi {{ $c['icon'] }}"></i>
                                </span>
                                <span class="nt-label">{{ $c['label'] }}</span>
                            </span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach($calCols as $c)
                        <td>
                            <div class="nt-items nt-scroll-5">
                                @forelse(($calItems[$c['key']] ?? []) as $it)
                                <div class="{{ !empty($it['red']) ? 'nt-red' : '' }}">
                                    <span class="nt-cal-txt">{{ $it['t'] }}</span>
                                </div>
                                @empty
                                @endforelse
                            </div>
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    {{-- ===== 下段：レシピ3ブロック ===== --}}
    <div class="nt-home-lower">

        {{-- 最近見たレシピ --}}
        <div class="nt-card">
            <div class="nt-card-head">
                <div class="nt-card-title">
                    <i class="bi bi-eye-fill"></i>
                    <span>最近見たレシピ</span>
                </div>
                <a href="{{ route('recipe.list') }}" class="nt-card-link">→ レシピ一覧へ</a>
            </div>

            <ul class="nt-card-list nt-card-list--scroll10">
                @forelse($recentRecipes as $r)
                <li class="nt-recipe-item">
                    <span class="nt-star">★</span>
                    <a class="nt-recipe-title" href="{{ route('recipe.detail', $r->id) }}">
                        {{ $r->title }}
                    </a>
                </li>
                @empty
                <li class="nt-muted">まだ閲覧履歴がありません</li>
                @endforelse
            </ul>
        </div>

        {{-- お気に入りレシピ --}}
        <div class="nt-card">
            <div class="nt-card-head">
                <div class="nt-card-title">
                    <i class="bi bi-heart-fill"></i>
                    <span>お気に入りレシピ</span>
                </div>
                <a href="{{ route('recipe.list') }}" class="nt-card-link">→ レシピ一覧へ</a>
            </div>

            <ul class="nt-card-list nt-card-list--scroll10">
                @forelse($favoriteRecipes as $r)
                <li class="nt-recipe-item">
                    <span class="nt-heart">♥</span>
                    <a class="nt-recipe-title" href="{{ route('recipe.detail', $r->id) }}">
                        {{ $r->title }}
                    </a>
                </li>
                @empty
                <li class="nt-muted">お気に入りがありません</li>
                @endforelse
            </ul>
        </div>

        {{-- おすすめレシピ --}}
        <div class="nt-card">
            <div class="nt-card-head">
                <div class="nt-card-title">
                    <i class="bi bi-hand-thumbs-up-fill"></i>
                    <span>おすすめレシピ</span>
                </div>
                <a href="{{ route('recipe.list') }}" class="nt-card-link">→ レシピ一覧へ</a>
            </div>

            @if(isset($recommendTags) && count($recommendTags) > 0)
            <div class="nt-reco-kwbar">
                食材：{{ implode('＋', $recommendTags->toArray()) }}
            </div>
            @endif

            <ul class="nt-card-list nt-card-list--scroll10">
                @forelse($recommendedRecipes as $r)
                <li class="nt-recipe-item">
                    <span class="nt-like">👍</span>
                    <a class="nt-recipe-title" href="{{ route('recipe.detail', $r->id) }}">
                        {{ $r->title }}
                    </a>
                </li>
                @empty
                <li class="nt-muted">おすすめが見つかりません</li>
                @endforelse
            </ul>
        </div>

    </div> {{-- /.nt-home-lower --}}

</div> {{-- /.nt-wrap --}}

@endsection