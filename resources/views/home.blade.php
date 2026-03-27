@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-10 text-[#4A4A4A]">

        {{-- ヘッダー --}}
        <div class="mb-12 flex justify-between items-end border-b border-[#EAE4DD] pb-6">
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
                                \Carbon\Carbon::parse($item->expiration_date)->startOfDay() < \Carbon\Carbon::today();
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
                                {{-- ★ onclickマジックを追加！（JSを呼び出す） --}}
                                <button type="button" onclick="deleteIngredient({{ $item->id }}, this)"
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
                                {{-- ★ onclickマジックを追加！（JSを呼び出す） --}}
                                <button type="button" onclick="deleteIngredient({{ $item->id }}, this)"
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
                <button
                    class="w-full bg-[#4A3F35] py-5 rounded-xl text-white text-[13px] font-bold tracking-[0.2em] hover:bg-[#333333] transition-all flex items-center justify-center gap-3 shadow-lg group">
                    <i class="bi bi-stars group-hover:rotate-12 transition-transform"></i>
                    在庫食材からAI献立を生成する
                </button>
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
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/home.js') }}"></script>
@endsection
