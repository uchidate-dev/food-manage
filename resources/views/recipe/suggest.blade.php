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

            {{-- 上部メッセージ --}}
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center bg-white border border-[#C1A173] text-[#C1A173] px-4 py-1.5 rounded-full text-[10px] font-bold tracking-[0.2em] shadow-sm mb-4">
                    <i class="bi bi-robot mr-2"></i> AI RECIPE SUGGESTION
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#8C7A6B]/80 tracking-tight mb-2">
                    こんな献立はいかがですか？
                </h1>
                <p class="text-[11px] font-bold text-gray-400 tracking-widest">
                    冷蔵庫の在庫から、あなたにぴったりのレシピを考えました！
                </p>
            </div>

            {{-- レシピカード本体 --}}
            <div class="bg-white rounded-3xl shadow-lg shadow-[#8C7A6B]/5 border border-[#EAE4DD] overflow-hidden mb-10">

                {{-- ヘッダー部分 --}}
                <div class="bg-[#FCFBFA] px-8 py-10 border-b border-[#EAE4DD] text-center">
                    <h2
                        class="text-3xl sm:text-4xl font-extrabold text-[#4A3F35] leading-tight tracking-wide mb-2 font-sans">
                        {{ $dummyAiRecipe['title'] }}
                    </h2>
                    <div class="w-12 h-1 bg-[#C1A173] mx-auto mt-6 rounded-full"></div>
                </div>

                <div class="p-8 sm:p-12 space-y-10">

                    {{-- キッズ向け・魔法の工夫 --}}
                    <div class="bg-orange-50/50 border border-orange-100 rounded-2xl p-6 relative">
                        <div
                            class="absolute -top-4 left-6 bg-white border border-orange-200 px-4 py-1 rounded-full text-[11px] font-bold text-orange-400 tracking-widest flex items-center shadow-sm">
                            <i class="bi bi-stars mr-1.5 text-orange-300"></i> キッズ向け・魔法の工夫
                        </div>
                        <p class="text-sm font-bold text-gray-700 leading-loose tracking-wide pt-3 px-2">
                            {{ $dummyAiRecipe['kids_tips'] }}
                        </p>
                    </div>

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
                                    {{-- 🌟 配列の形でデータを取り出すように変更！ --}}
                                    @if (isset($dummyAiRecipe['ingredients']) && is_array($dummyAiRecipe['ingredients']))
                                        @foreach ($dummyAiRecipe['ingredients'] as $ing)
                                            <li
                                                class="flex items-start border-b border-gray-100 pb-2 last:border-0 last:pb-0">
                                                <i class="bi bi-check2 text-[#C1A173] mr-2 text-lg leading-none mt-0.5"></i>
                                                <span class="pt-0.5">{{ $ing['name'] }}：{{ $ing['quantity'] }}</span>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="text-gray-400 text-xs">材料データがありません</li>
                                    @endif
                                </ul>
                            </div>

                            {{-- 栄養グラフ --}}
                            <div class="bg-white rounded-2xl p-6 border border-[#EAE4DD] shadow-sm">
                                <h3 class="text-[13px] font-bold text-[#8C7A6B] tracking-widest mb-6 flex items-center">
                                    <i class="bi bi-bar-chart-line mr-2 text-[#C1A173]"></i> 栄養バランス
                                </h3>
                                <div class="space-y-4">
                                    @foreach ($dummyAiRecipe['nutrition'] as $label => $score)
                                        <div class="flex items-center">
                                            <span
                                                class="w-20 text-[11px] font-bold text-gray-500 tracking-widest">{{ $label }}</span>
                                            <div
                                                class="flex-1 h-2.5 bg-[#F5F2EF] rounded-full overflow-hidden ml-3 relative">
                                                <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-[#D4C3A3] to-[#C1A173] rounded-full transition-all duration-1000 ease-out"
                                                    style="width: {{ ($score / 5) * 100 }}%"></div>
                                            </div>
                                            <span
                                                class="w-10 text-right text-[11px] font-bold text-[#8C7A6B] tracking-widest">{{ $score }}/5</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        {{-- 右カラム：作り方 ＆ メモ --}}
                        <div class="lg:col-span-7 space-y-8">

                            {{-- 手順 --}}
                            <div>
                                <h3
                                    class="text-[13px] font-bold text-[#8C7A6B] tracking-widest border-b border-[#EAE4DD] pb-3 mb-6 flex items-center">
                                    <i class="bi bi-magic mr-2 text-[#C1A173]"></i> 作り方
                                </h3>
                                <div class="space-y-6">
                                    @foreach ($dummyAiRecipe['steps'] as $index => $step)
                                        <div class="flex gap-4 items-start">
                                            <div
                                                class="flex-shrink-0 w-8 h-8 rounded-full bg-[#8C7A6B] text-white flex items-center justify-center text-xs font-bold shadow-md shadow-[#8C7A6B]/20">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="text-sm text-gray-700 leading-relaxed font-medium pt-1.5">
                                                {{ $step }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- 大人向け・ちょい足しアレンジ --}}
                            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 flex items-start gap-3 mt-4">
                                <i class="bi bi-lightbulb text-xl text-[#C1A173] pt-0.5"></i>
                                <div class="flex-1">
                                    <span
                                        class="text-[10px] font-bold text-[#C1A173] tracking-widest block mb-1.5">大人向けアレンジ</span>
                                    <p class="text-xs font-bold text-gray-600 leading-loose">
                                        {{ $dummyAiRecipe['memo'] }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div> {{-- グリッド終了 --}}
                </div> {{-- レシピカード本体の余白終了 --}}
            </div> {{-- レシピカード終了 --}}

            {{-- アクションボタン --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-8 pb-10">
                <a href="/recipe/suggest" onclick="showLoading(event, this.href)"
                    class="w-full sm:w-auto px-8 py-3.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors tracking-widest text-center border border-[#EAE4DD] bg-white rounded-xl shadow-sm hover:bg-gray-50 flex items-center justify-center whitespace-nowrap">
                    <i class="bi bi-arrow-clockwise mr-2"></i> 別の献立を提案してもらう
                </a>
                <form action="{{ route('recipe.suggest.save') }}" method="POST" class="w-full sm:w-auto m-0">
                    @csrf
                    {{-- AIが考えたJSONデータを丸ごとコントローラーに送る準備！ --}}
                    <input type="hidden" name="ai_recipe_data" value="{{ json_encode($dummyAiRecipe) }}">

                    <button type="submit"
                        class="w-full sm:w-auto bg-[#C1A173] px-10 py-3.5 rounded-xl text-xs font-bold text-white shadow-lg shadow-[#C1A173]/30 hover:bg-[#A88C61] hover:-translate-y-1 transition-all tracking-[0.2em] flex items-center justify-center whitespace-nowrap">
                        <i class="bi bi-bookmark-heart mr-2 text-lg"></i> レシピ帳に保存する
                    </button>
                </form>
            </div>
        </div>

        {{-- 共通パーツのロボット --}}
        @include('components.loading-robot')

    </div>
@endsection
