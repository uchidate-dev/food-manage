@extends('layouts.app')

@section('content')

    @php
        $backUrl = session('recipes_back_url', url()->previous());
        $isFavorited = false;
        if (auth()->check()) {
            $isFavorited = auth()->user()->favoriteRecipes()->where('recipes.id', $recipe->id)->exists();
        }
    @endphp

    <div class="bg-[#FAF9F6] text-[#4A4A4A] min-h-screen py-8 px-4 sm:px-6 pb-24">

        <div class="max-w-4xl mx-auto">

            {{-- 上部ナビゲーション（戻る ＆ アクション） --}}
            <div class="flex items-center justify-between mb-6">
                <a href="{{ $backUrl }}"
                    class="inline-flex items-center text-[11px] font-bold text-gray-400 hover:text-[#C1A173] transition-colors tracking-widest uppercase">
                    <i class="bi bi-arrow-left mr-2 text-lg"></i> 一覧に戻る
                </a>

                <div class="flex items-center gap-4">
                    {{-- お気に入りボタン --}}
                    <button type="button"
                        class="favorite-btn text-2xl transition-transform hover:scale-110 {{ $isFavorited ? 'text-red-400' : 'text-gray-300' }}"
                        data-id="{{ $recipe->id }}" title="お気に入りに追加">
                        <i class="bi {{ $isFavorited ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                    </button>

                    {{-- 編集ボタン（自分用メモを書き足す時用！） --}}
                    @if (auth()->check() && auth()->id() === $recipe->user_id)
                        <a href="{{ url('/recipe_update/' . $recipe->id) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-[#C1A173] bg-white px-5 py-2.5 text-[11px] font-bold text-[#C1A173] hover:bg-[#FAF9F6] transition-all tracking-widest shadow-sm">
                            <i class="bi bi-pencil mr-1.5"></i> メモを編集
                        </a>
                    @endif
                </div>
            </div>

            {{-- 👑 レシピカード本体 --}}
            <div class="bg-white rounded-3xl shadow-sm border border-[#EAE4DD] overflow-hidden">

                {{-- ヘッダー部分（タイトルとカテゴリー） --}}
                <div class="bg-[#FCFBFA] px-8 py-10 border-b border-[#EAE4DD] text-center relative">
                    {{-- カテゴリーバッジ --}}
                    <span
                        class="inline-block bg-[#F5F2EF] text-[#8C7A6B] text-[10px] font-bold px-3 py-1 rounded-full tracking-widest mb-4">
                        {{ optional($recipe->category)->name ?? '未分類' }}
                    </span>

                    {{-- タイトル --}}
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 leading-tight tracking-wide mb-2"
                        style="font-family: ui-serif, Georgia, Cambria, 'Times New Roman', Times, serif;">
                        {{ $recipe->title }}
                    </h1>
                    <div class="w-12 h-0.5 bg-[#C1A173] mx-auto mt-6"></div>
                </div>

                {{-- コンテンツ部分（2カラムレイアウト） --}}
                <div class="p-8 sm:p-12 grid grid-cols-1 lg:grid-cols-12 gap-12">

                    {{-- 左カラム：材料 ＆ 参考URL --}}
                    <div class="lg:col-span-5 space-y-8">

                        {{-- 完成画像（もしあれば） --}}
                        @if (!empty($recipe->finished_image))
                            <div
                                class="aspect-square rounded-2xl overflow-hidden bg-[#FAF9F6] border border-[#EAE4DD] shadow-inner">
                                <img src="{{ $recipe->finished_image }}" alt="完成写真" class="w-full h-full object-cover">
                            </div>
                        @endif

                        {{-- 材料リスト --}}
                        <div class="bg-[#FAF9F6] rounded-2xl p-6 border border-[#EAE4DD]">
                            <h3
                                class="text-[13px] font-bold text-[#8C7A6B] tracking-widest border-b border-[#EAE4DD] pb-3 mb-4 flex items-center">
                                <i class="bi bi-basket mr-2 text-[#C1A173]"></i> 材料
                            </h3>
                            @if (!empty($recipe->ingredients))
                                <div class="text-sm text-gray-700 leading-loose whitespace-pre-wrap font-medium">
                                    {{ trim($recipe->ingredients) }}</div>
                            @else
                                <p class="text-xs text-gray-400 font-bold tracking-widest">材料が登録されていません</p>
                            @endif
                        </div>

                        {{-- 参考URL（もしあれば） --}}
                        @if (!empty($recipe->url))
                            <div>
                                <h3 class="text-[11px] font-bold text-gray-400 tracking-widest mb-2"><i
                                        class="bi bi-link-45deg mr-1"></i> 参考レシピ</h3>
                                <a href="{{ $recipe->url }}" target="_blank" rel="noopener"
                                    class="text-xs text-[#C1A173] hover:underline break-all font-bold">
                                    {{ $recipe->url }}
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- 右カラム：作り方 ＆ メモ --}}
                    <div class="lg:col-span-7 space-y-8">

                        {{-- 料理メモ（AIの解説などはここに入る想定） --}}
                        <div>
                            <h3
                                class="text-[13px] font-bold text-[#8C7A6B] tracking-widest border-b border-[#EAE4DD] pb-3 mb-4 flex items-center">
                                <i class="bi bi-journal-text mr-2 text-[#C1A173]"></i> 作り方・料理メモ
                            </h3>
                            @if (!empty($recipe->memo))
                                <div
                                    class="text-sm text-gray-700 leading-loose whitespace-pre-wrap font-medium bg-white p-2 rounded-xl">
                                    {{ trim($recipe->memo) }}</div>
                            @else
                                <p class="text-xs text-gray-400 font-bold tracking-widest px-2">メモはありません</p>
                            @endif
                        </div>

                        {{-- 手順（昔の名残。データがあれば表示する） --}}
                        @php $hasSteps = false; @endphp
                        @for ($i = 1; $i <= 15; $i++)
                            @php
                                $textKey = 'step' . $i;
                                if (!empty($recipe->$textKey)) {
                                    $hasSteps = true;
                                }
                            @endphp
                        @endfor

                        @if ($hasSteps)
                            <div class="space-y-6 pt-4">
                                @for ($i = 1; $i <= 15; $i++)
                                    @php
                                        $textKey = 'step' . $i;
                                        $text = $recipe->$textKey ?? null;
                                    @endphp
                                    @if (!empty($text))
                                        <div class="flex gap-4 items-start">
                                            <div
                                                class="flex-shrink-0 w-7 h-7 rounded-full bg-[#F5F2EF] text-[#8C7A6B] flex items-center justify-center text-[11px] font-bold mt-0.5">
                                                {{ $i }}
                                            </div>
                                            <div
                                                class="text-sm text-gray-700 leading-relaxed font-medium pt-1 whitespace-pre-wrap">
                                                {{ trim($text) }}</div>
                                        </div>
                                    @endif
                                @endfor
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('js/recipe_favorite.js') }}"></script>
@endsection
