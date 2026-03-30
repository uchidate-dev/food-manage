@extends('layouts.app')

@section('content')
    <div class="bg-[#FAF9F6] text-[#4A4A4A] min-h-screen py-8 px-4 sm:px-6 pb-24 relative overflow-hidden">

        {{-- 背景装飾 --}}
        <div
            class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-gradient-to-br from-[#C1A173]/10 to-transparent rounded-full blur-3xl -z-10">
        </div>
        <div
            class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-gradient-to-tl from-[#8C7A6B]/10 to-transparent rounded-full blur-3xl -z-10">
        </div>

        <div class="max-w-4xl mx-auto">

            {{-- 上部ナビゲーション＆メッセージ --}}
            <div class="flex justify-between items-center mb-8">
                <a href="/recipe_list"
                    class="text-[11px] font-bold text-gray-400 hover:text-[#C1A173] tracking-widest transition-colors flex items-center">
                    <i class="bi bi-chevron-left mr-1"></i> レシピ一覧へ戻る
                </a>

                {{-- お気に入りボタン --}}
                <button type="button" class="favorite-btn text-2xl transition-transform hover:scale-110"
                    data-id="{{ $recipe->id }}">
                    <i class="bi {{ $isFavorited ? 'bi-heart-fill text-[#C1A173]' : 'bi-heart text-gray-300' }}"></i>
                </button>
            </div>

            {{-- レシピカード本体 --}}
            <div class="bg-white rounded-3xl shadow-lg shadow-[#8C7A6B]/5 border border-[#EAE4DD] overflow-hidden mb-10">

                {{-- ヘッダー部分 --}}
                <div class="bg-[#FCFBFA] px-8 py-10 border-b border-[#EAE4DD] text-center relative">
                    {{-- カテゴリバッジ --}}
                    @if ($recipe->category)
                        <div class="absolute top-6 left-1/2 transform -translate-x-1/2">
                            <span
                                class="bg-[#F5F2EF] text-[#8C7A6B] px-3 py-1 rounded-full text-[10px] font-bold tracking-widest border border-[#EAE4DD]">
                                {{ $recipe->category->name }}
                            </span>
                        </div>
                    @endif

                    <h2
                        class="text-3xl sm:text-4xl font-extrabold text-[#4A3F35] leading-tight tracking-wide mb-2 mt-4 font-sans">
                        {{ $recipe->title }}
                    </h2>
                    <div class="w-12 h-1 bg-[#C1A173] mx-auto mt-6 rounded-full"></div>
                </div>

                <div class="p-8 sm:p-12 space-y-10">

                    {{-- キッズ向け（データがある時だけ表示） --}}
                    @if ($recipe->kids_tips)
                        <div class="bg-orange-50/50 border border-orange-100 rounded-2xl p-6 relative">
                            <div
                                class="absolute -top-4 left-6 bg-white border border-orange-200 px-4 py-1 rounded-full text-[11px] font-bold text-orange-400 tracking-widest flex items-center shadow-sm">
                                <i class="bi bi-stars mr-1.5 text-orange-300"></i> キッズ向け・魔法の工夫
                            </div>
                            <p class="text-sm font-bold text-gray-700 leading-loose tracking-wide pt-3 px-2">
                                {{ $recipe->kids_tips }}
                            </p>
                        </div>
                    @endif

                    {{-- カラムを分けるグリッド --}}
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                        {{-- 左カラム：材料 ＆ 栄養グラフ --}}
                        <div class="lg:col-span-5 space-y-8">

                            {{-- 材料 --}}
                            <div class="bg-[#FAF9F6] rounded-2xl p-6 border border-[#EAE4DD]">
                                <h3
                                    class="text-[13px] font-bold text-[#8C7A6B] tracking-widest border-b border-[#EAE4DD] pb-3 mb-4 flex items-center">
                                    <i class="bi bi-basket mr-2 text-[#C1A173]"></i> 使う材料
                                </h3>
                                <ul class="text-sm text-gray-700 font-medium space-y-2">
                                    @forelse ($ingredientsList as $ing)
                                        <li class="flex items-start border-b border-gray-100 pb-2 last:border-0 last:pb-0">
                                            <i class="bi bi-check2 text-[#C1A173] mr-2 text-lg leading-none mt-0.5"></i>
                                            <span class="pt-0.5">{{ $ing }}</span>
                                        </li>
                                    @empty
                                        <li class="text-gray-400 text-xs">材料が登録されていません</li>
                                    @endforelse
                                </ul>
                            </div>

                            {{-- 栄養グラフ（データがある時だけ表示） --}}
                            @if (!empty($recipe->nutrition) && is_array($recipe->nutrition))
                                <div class="bg-white rounded-2xl p-6 border border-[#EAE4DD] shadow-sm">
                                    <h3 class="text-[13px] font-bold text-[#8C7A6B] tracking-widest mb-6 flex items-center">
                                        <i class="bi bi-bar-chart-line mr-2 text-[#C1A173]"></i> 栄養バランス
                                    </h3>
                                    <div class="space-y-4">
                                        @foreach ($recipe->nutrition as $label => $score)
                                            <div class="flex items-center">
                                                <span
                                                    class="w-20 text-[11px] font-bold text-gray-500 tracking-widest">{{ $label }}</span>
                                                <div
                                                    class="flex-1 h-2.5 bg-[#F5F2EF] rounded-full overflow-hidden ml-3 relative">
                                                    <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-[#D4C3A3] to-[#C1A173] rounded-full"
                                                        style="width: {{ (intval($score) / 5) * 100 }}%"></div>
                                                </div>
                                                <span
                                                    class="w-10 text-right text-[11px] font-bold text-[#8C7A6B] tracking-widest">{{ $score }}/5</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>

                        {{-- 右カラム：作り方 ＆ メモ --}}
                        <div class="lg:col-span-7 space-y-8">

                            {{-- 手順（データがある時だけ表示） --}}
                            @if (!empty($recipe->steps) && is_array($recipe->steps))
                                <div>
                                    <h3
                                        class="text-[13px] font-bold text-[#8C7A6B] tracking-widest border-b border-[#EAE4DD] pb-3 mb-6 flex items-center">
                                        <i class="bi bi-magic mr-2 text-[#C1A173]"></i> 作り方
                                    </h3>
                                    <div class="space-y-6">
                                        @foreach ($recipe->steps as $index => $step)
                                            <div class="flex gap-4 items-start">
                                                <div
                                                    class="flex-shrink-0 w-8 h-8 rounded-full bg-[#8C7A6B] text-white flex items-center justify-center text-xs font-bold shadow-md shadow-[#8C7A6B]/20">
                                                    {{ $index + 1 }}
                                                </div>
                                                <div class="text-sm text-gray-700 leading-relaxed font-medium pt-1.5">
                                                    {{ $step }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- メモ・アレンジ --}}
                            @if ($recipe->memo)
                                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 flex items-start gap-3 mt-4">
                                    <i class="bi bi-lightbulb text-xl text-[#C1A173] pt-0.5"></i>
                                    <div class="flex-1">
                                        <span
                                            class="text-[10px] font-bold text-[#C1A173] tracking-widest block mb-1.5">メモ・大人向けアレンジ</span>
                                        <p class="text-xs font-bold text-gray-600 leading-loose">
                                            {!! nl2br(e($recipe->memo)) !!}
                                        </p>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div> {{-- グリッド終了 --}}
                </div>
            </div> {{-- レシピカード終了 --}}

            {{-- 編集・削除ボタン --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pb-10">
                <a href="{{ route('recipe.edit', $recipe->id) }}"
                    class="w-full sm:w-auto px-10 py-3.5 bg-white text-[#C1A173] border border-[#C1A173] rounded-xl text-xs font-bold hover:bg-[#FAF9F6] transition-colors tracking-widest text-center shadow-sm">
                    <i class="bi bi-pencil mr-1"></i> 編集する
                </a>

                {{-- 削除モーダルボタン --}}
                <button type="button"
                    onclick="openSharedDeleteModal('{{ route('recipe.destroy', $recipe->id) }}', '{{ $recipe->title }}')"
                    class="w-full sm:w-auto px-8 py-3.5 text-xs font-bold text-red-400 hover:text-red-600 transition-colors tracking-widest text-center bg-white border border-red-100 rounded-xl shadow-sm hover:bg-red-50">
                    <i class="bi bi-trash mr-1"></i> 削除
                </button>
            </div>

        </div>

        {{-- 共通パーツ：削除モーダルを召喚！ --}}
        @include('components.delete-modal')

    </div>

@endsection

@section('js')
    <script src="{{ asset('js/recipe_favorite.js') }}"></script>
    <script src="{{ asset('js/shared_components.js') }}"></script>
@endsection
