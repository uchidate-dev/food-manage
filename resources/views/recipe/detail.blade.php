@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/recipe_detail.css') }}">
@endsection

@section('content')

@php
    $backUrl = session('recipes_back_url', url()->previous());

    $isFavorited = false;

    if (auth()->check()) {
        $isFavorited = auth()->user()
            ->favoriteRecipes()
            ->where('recipes.id', $recipe->id)
            ->exists();
    }
@endphp

<div class="bg-emerald-50 text-stone-800 min-h-screen">

    <div class="max-w-6xl mx-auto px-8 py-10">

        {{-- 戻る + ボタン --}}
        <div class="flex items-center justify-between">

            <a href="{{ $backUrl }}"
               class="inline-flex items-center gap-2 font-extrabold text-stone-700 no-underline hover:no-underline text-lg">

                <span class="text-xl">←</span>
                前に戻る

            </a>

            <div class="flex items-center gap-4">

                <button
                    type="button"
                    class="favorite-btn text-4xl leading-none {{ $isFavorited ? 'text-pink-600' : 'text-stone-400' }}"
                    data-id="{{ $recipe->id }}"
                    title="お気に入り切替"
                    aria-label="お気に入り切替">

                    {{ $isFavorited ? '♥' : '♡' }}

                </button>

                @if(auth()->check() && auth()->id() === $recipe->user_id)

                    <a href="{{ url('/recipe_update/'.$recipe->id) }}"
                       class="inline-flex items-center justify-center rounded-lg border border-stone-400 bg-stone-200 px-8 py-2 font-extrabold text-stone-700 hover:bg-stone-100">

                        編　集

                    </a>

                @endif

            </div>

        </div>

        {{-- タイトル --}}
        <div class="mt-10">

            @if(!empty($recipe->title))

                <div class="relative inline-block">

                    <h1 class="relative z-10 text-5xl font-extrabold tracking-wide text-stone-700">
                        {{ $recipe->title }}
                    </h1>

                    <div class="absolute left-0 bottom-0 h-2 w-full rounded bg-yellow-300/80 z-0"></div>

                </div>

            @endif

            {{-- 参考元レシピ --}}
            @if(!empty($recipe->url))
                <div class="mt-8">
                    <div class="flex items-start gap-6">
                        <div class="section-title-no-line shrink-0">
                            参考元レシピ
                        </div>

                        <div class="flex-1 text-stone-600 font-bold break-all text-lg">
                            <a
                                href="{{ $recipe->url }}"
                                target="_blank"
                                rel="noopener"
                                class="underline">
                                {{ $recipe->url }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <div class="mt-12 grid grid-cols-12 gap-x-4">

            {{-- 左カラム --}}
            <div class="col-span-12 md:col-span-6">

                <div class="space-y-10">

                    {{-- 完成画像 --}}
                    @if(!empty($recipe->finished_image))

                        <div class="w-[360px] max-w-full">

                            <div class="aspect-square rounded-xl overflow-hidden bg-emerald-50 flex items-center justify-center">

                                <img
                                    src="{{ $recipe->finished_image }}"
                                    alt="完成写真"
                                    class="w-full h-full object-contain">

                            </div>

                        </div>

                    @endif

                    {{-- 料理メモ --}}
                    @if(!empty($recipe->memo))

                        <div class="w-[360px] max-w-full">

                            <div class="section-title">
                                料理メモ
                            </div>

                            <div class="whitespace-pre-wrap font-bold text-stone-700 text-xl">{{ trim($recipe->memo) }}</div>

                        </div>

                    @endif

                    {{-- 材料 --}}
                    @if(!empty($recipe->ingredients))

                        <div class="w-[360px] max-w-full">

                            <div class="section-title">
                                材料
                            </div>

                            <div class="rounded-xl border-2 border-stone-400 bg-white/80 px-6 py-3 font-bold text-stone-700 text-xl whitespace-pre-wrap">{{ trim($recipe->ingredients) }}</div>

                        </div>

                    @endif

                </div>

            </div>

            {{-- 右カラム --}}
            <div class="col-span-12 md:col-span-6">

                <div class="section-title">
                    作り方
                </div>

                <div class="space-y-8">

                    @for($i = 1; $i <= 15; $i++)

                        @php
                            $imgKey  = 'step_image'.$i;
                            $textKey = 'step'.$i;

                            $img  = $recipe->$imgKey ?? null;
                            $text = $recipe->$textKey ?? null;
                        @endphp

                        {{-- 手順画像 --}}
                        @if(!empty($img))

                            <div class="w-[520px] max-w-full">

                                <div class="aspect-[4/3] rounded-xl overflow-hidden bg-emerald-50 flex items-center justify-center">

                                    <img
                                        src="{{ $img }}"
                                        alt="Step{{ $i }}写真"
                                        class="w-full h-full object-contain">

                                </div>

                            </div>

                        @endif

                        {{-- 手順テキスト --}}
                        @if(!empty($text))

                            <div class="w-[520px] max-w-full">

                                <div class="whitespace-pre-wrap font-bold text-stone-700 text-2xl">{{ trim($text) }}</div>

                            </div>

                        @endif

                    @endfor

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

@section('js')
  <script src="{{ asset('js/recipe_favorite.js') }}"></script>
@endsection