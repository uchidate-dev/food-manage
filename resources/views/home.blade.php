@extends('layouts.app')

@section('content')
    <div class="bg-[#FAF9F6] text-[#4A4A4A] min-h-screen py-8 relative overflow-hidden">

        {{-- 背景装飾（他画面と統一✨） --}}
        <div
            class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-gradient-to-br from-[#C1A173]/10 to-transparent rounded-full blur-3xl -z-10">
        </div>
        <div
            class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-gradient-to-tl from-[#8C7A6B]/10 to-transparent rounded-full blur-3xl -z-10">
        </div>

        <div class="max-w-3xl mx-auto px-6 py-4">

            {{-- ヘッダー --}}
            <div class="mb-8 flex justify-between items-end border-b border-[#EAE4DD] pb-6">
                <div>
                    <h1 class="text-2xl font-bold text-[#8C7A6B] tracking-tight">今日の冷蔵庫レポート</h1>
                    <p class="text-[11px] text-[#C1A173] font-bold tracking-[0.1em] mt-1">
                        ロスを減らして、美味しい食卓を。
                    </p>
                </div>
                <div class="text-[10px] font-bold text-gray-400 tracking-widest">
                    {{ date('Y.m.d') }}
                </div>
            </div>
            {{-- 在庫サマリー --}}
            @php
                $urgentCount =
                    $expiredItems->count() + $soonItems->where('expiration_date', '<=', date('Y-m-d'))->count();
                $soonCount = $soonItems->where('expiration_date', '>', date('Y-m-d'))->count();
            @endphp
            <div class="grid grid-cols-2 gap-4 mb-10">
                <div class="bg-white rounded-2xl p-5 border border-[#EAE4DD] shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 tracking-widest mb-1">早めに使いたい食材</p>
                        <p class="text-2xl font-bold text-red-400">{{ $urgentCount }}<span
                                class="text-xs ml-1 text-gray-400">件</span></p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-400 text-lg">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-[#EAE4DD] shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 tracking-widest mb-1">近いうちに使う食材</p>
                        <p class="text-2xl font-bold text-[#C1A173]">{{ $soonCount }}<span
                                class="text-xs ml-1 text-gray-400">件</span></p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-full bg-[#FAF9F6] flex items-center justify-center text-[#C1A173] text-lg">
                        <i class="bi bi-check2-square"></i>
                    </div>
                </div>
            </div>

            <div class="space-y-10">

                {{-- 要対応エリア --}}
                <section>
                    <div class="flex items-center gap-2 mb-4 text-red-500">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <h2 class="text-xs font-bold text-gray-500 tracking-widest">早めに使いたい食材</h2>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-[#FEE2E2] overflow-hidden">
                        @forelse ($expiredItems->merge($soonItems->where('expiration_date', '<=', date('Y-m-d')))
                            as $item)
                            @php
                                $isExpired =
                                    \Carbon\Carbon::parse($item->expiration_date)->startOfDay() <
                                    \Carbon\Carbon::today();
                                $badgeText = $isExpired ? '期限切れ😭' : '本日中！';
                                $badgeClass = $isExpired ? 'bg-red-500' : 'bg-red-400';

                                $iconMap = [
                                    1 => '🥦',
                                    2 => '🥩',
                                    3 => '🐟',
                                    4 => '🥚',
                                    5 => '🧈',
                                    6 => '🍜',
                                    7 => '🍚',
                                    8 => '❄️',
                                    9 => '🧺',
                                ];
                                $icon = $iconMap[$item->ingredient_cat_id] ?? '🧺';
                            @endphp
                            <div
                                class="px-6 py-5 flex justify-between items-center hover:bg-[#FFF5F5] transition-colors border-b last:border-0 border-gray-50">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-sm shadow-inner">
                                        {{ $icon }}</div>
                                    <span
                                        class="text-[10px] font-bold text-white {{ $badgeClass }} px-2 py-1 rounded-full shadow-sm">{{ $badgeText }}</span>
                                    <span class="text-sm font-bold text-gray-700 tracking-wide">{{ $item->name }}</span>
                                </div>
                                <div class="flex items-center gap-6">
                                    <span
                                        class="text-sm font-bold text-red-400 font-mono">{{ \Carbon\Carbon::parse($item->expiration_date)->format('m/d') }}</span>
                                    {{-- ★ 共通の削除モーダルを呼び出す！ --}}
                                    <button type="button"
                                        onclick="openSharedDeleteModal('/ingredient_delete/{{ $item->id }}', '{{ $item->name }}')"
                                        class="text-gray-200 hover:text-red-400 transition-all">
                                        <i class="bi bi-trash3 text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="px-6 py-12 text-center text-xs text-gray-400 tracking-widest bg-gray-50/20">
                                現在、お急ぎの食材はありません ✨
                            </p>
                        @endforelse
                    </div>
                </section>

                {{-- 近日中の救済エリア --}}
                <section>
                    <div class="flex items-center gap-2 mb-4 text-[#C1A173]">
                        <i class="bi bi-check2-square-fill"></i>
                        <h2 class="text-xs font-bold text-gray-500 tracking-widest">近いうちに使う食材</h2>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-[#EAE4DD] overflow-hidden">
                        @forelse ($soonItems->where('expiration_date', '>', date('Y-m-d')) as $item)
                            @php
                                $iconMap = [
                                    1 => '🥦',
                                    2 => '🥩',
                                    3 => '🐟',
                                    4 => '🥚',
                                    5 => '🧈',
                                    6 => '🍜',
                                    7 => '🍚',
                                    8 => '❄️',
                                    9 => '🧺',
                                ];
                                $icon = $iconMap[$item->ingredient_cat_id] ?? '🧺';
                            @endphp
                            <div class="px-6 py-5 flex justify-between items-center border-b last:border-0 border-gray-50">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-8 h-8 rounded-full bg-[#FAF9F6] flex items-center justify-center text-sm shadow-inner">
                                        {{ $icon }}</div>
                                    <span class="text-sm font-bold text-gray-700 tracking-wide">{{ $item->name }}</span>
                                </div>
                                <div class="flex items-center gap-6">
                                    <span class="text-sm font-bold text-gray-500 font-mono">期限:
                                        {{ \Carbon\Carbon::parse($item->expiration_date)->format('m/d') }}</span>
                                    {{-- ★ 共通の削除モーダルを呼び出す！ --}}
                                    <button type="button"
                                        onclick="openSharedDeleteModal('/ingredient_delete/{{ $item->id }}', '{{ $item->name }}')"
                                        class="text-gray-200 hover:text-[#C1A173] transition-all text-xl">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="px-6 py-12 text-center text-xs text-gray-400 tracking-widest">
                                近日中に期限を迎える食材はありません。
                            </p>
                        @endforelse
                    </div>
                </section>

                {{-- AIボタン --}}
                <div class="pt-2">
                    <a href="/recipe/suggest" onclick="showLoading()"
                        class="w-full bg-[#4A3F35] py-5 rounded-xl text-white text-[13px] font-bold tracking-[0.2em] hover:bg-[#333333] transition-all flex items-center justify-center gap-3 shadow-lg group block text-center">
                        <i class="bi bi-stars group-hover:rotate-12 transition-transform"></i>
                        在庫食材からAI献立を生成する
                    </a>
                    <p class="text-center text-[10px] text-gray-300 mt-4 tracking-widest font-medium">
                        10年の在庫管理ロジックに基づき、最適なレシピを提案します
                    </p>
                </div>

            </div>

            <div class="mt-16 text-center">
                <a href="{{ url('/ingredient_list') }}"
                    class="text-[11px] font-bold text-[#8C7A6B] tracking-[0.2em] hover:text-[#C1A173] transition-colors border-b-2 border-[#EAE4DD] pb-1">
                    すべての食材を見る ❯
                </a>
            </div>

            {{--  共通パーツ --}}
            @include('components.loading-robot')
            @include('components.delete-modal')

        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/home.js') }}"></script>
    <script src="{{ asset('js/shared_components.js') }}"></script>
@endsection
