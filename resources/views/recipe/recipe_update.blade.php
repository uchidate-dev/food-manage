@extends('layouts.app')

@section('content')
    <div class="bg-[#FAF9F6] text-[#4A4A4A] min-h-screen py-8 px-4 sm:px-6 pb-24 relative overflow-hidden">

        {{--  背景装飾 --}}
        <div
            class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-gradient-to-br from-[#C1A173]/10 to-transparent rounded-full blur-3xl -z-10">
        </div>
        <div
            class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-gradient-to-tl from-[#8C7A6B]/10 to-transparent rounded-full blur-3xl -z-10">
        </div>

        <div class="max-w-5xl mx-auto">

            {{-- 上部ナビゲーション --}}
            <div class="flex items-center justify-between mb-8">
                <a href="/recipe_list"
                    class="inline-flex items-center text-[11px] font-bold text-gray-400 hover:text-[#C1A173] transition-colors tracking-widest uppercase">
                    <i class="bi bi-arrow-left mr-2 text-lg"></i> 一覧に戻る
                </a>
                <h1 class="text-xl font-bold text-[#8C7A6B] tracking-widest font-logo">レシピを編集</h1>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-5 mb-6 rounded-r-xl shadow-sm">
                    <ul class="list-disc pl-5 text-[11px] text-red-500 font-bold tracking-widest space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('recipe.update', $recipe->id) }}" class="space-y-8">
                @csrf
                @method('PATCH')

                <div
                    class="bg-white rounded-3xl shadow-lg shadow-[#8C7A6B]/5 border border-[#EAE4DD] overflow-hidden p-8 sm:p-12">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                        {{-- ================= 左カラム ================= --}}
                        <div class="lg:col-span-5 space-y-8">

                            {{-- レシピ名 --}}
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                    <i class="bi bi-pencil mr-1 text-[#C1A173]"></i> レシピ名 <span
                                        class="text-red-400 ml-1">*</span>
                                </label>
                                <input type="text" name="title" value="{{ old('title', $recipe->title) }}" required
                                    class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all">
                            </div>

                            {{-- カテゴリ --}}
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                    <i class="bi bi-tag mr-1 text-[#C1A173]"></i> カテゴリー <span
                                        class="text-red-400 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <select name="recipe_category_id" required
                                        class="w-full appearance-none bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all cursor-pointer">
                                        <option value="" disabled>選択してください</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('recipe_category_id', $recipe->recipe_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#C1A173]">
                                        <i class="bi bi-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- 材料 --}}
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                    <i class="bi bi-basket mr-1 text-[#C1A173]"></i> 材料
                                </label>
                                <textarea name="ingredients" rows="8" placeholder="卵: 1個&#13;&#10;ごはん: 100g"
                                    class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-medium text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all resize-none">{{ old('ingredients', $recipe->ingredients) }}</textarea>
                            </div>
                        </div>

                        {{-- ================= 右カラム ================= --}}
                        <div class="lg:col-span-7 space-y-8">

                            {{-- キッズ向け・魔法の工夫 --}}
                            <div class="bg-orange-50/50 border border-orange-100 rounded-2xl p-6 relative">
                                <label
                                    class="absolute -top-4 left-6 bg-white border border-orange-200 px-4 py-1 rounded-full text-[11px] font-bold text-orange-400 tracking-widest flex items-center shadow-sm">
                                    <i class="bi bi-stars mr-1.5 text-orange-300"></i> キッズ向け・魔法の工夫
                                </label>
                                <textarea name="kids_tips" rows="3" placeholder="子供が食べやすくなる工夫を入力..."
                                    class="w-full bg-transparent border-none px-2 pt-3 text-sm font-bold text-gray-700 focus:ring-0 resize-none">{{ old('kids_tips', $recipe->kids_tips) }}</textarea>
                            </div>

                            {{-- 作り方（AIのsteps配列をテキストエリアで編集できるように変換） --}}
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center border-b border-[#EAE4DD] pb-3">
                                    <i class="bi bi-magic mr-1 text-[#C1A173]"></i> 作り方
                                </label>
                                @php
                                    // 配列のstepsを改行区切りのテキストに変換
                                    $stepsText = '';
                                    if (is_array($recipe->steps)) {
                                        $stepsText = implode("\n", $recipe->steps);
                                    } elseif (is_string($recipe->steps)) {
                                        // 念のため文字列で入っていた場合の処理
                                        $stepsText = $recipe->steps;
                                    }
                                @endphp
                                <textarea name="steps" rows="6" placeholder="作り方を入力..."
                                    class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-4 text-sm font-medium leading-relaxed text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all resize-none">{{ old('steps', $stepsText) }}</textarea>
                                <p class="text-[10px] text-gray-400 mt-2 ml-1">※各手順は改行で区切ってください。</p>
                            </div>

                            {{-- メモ・大人向けアレンジ --}}
                            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 mt-4">
                                <label
                                    class="block text-[10px] font-bold text-[#C1A173] tracking-widest mb-2 flex items-center">
                                    <i class="bi bi-lightbulb mr-1.5 text-[#C1A173] text-sm"></i> メモ・大人向けアレンジ
                                </label>
                                <textarea name="memo" rows="4" placeholder="大人用のお弁当に入れる際のアレンジなどを入力..."
                                    class="w-full bg-transparent border-none px-2 text-xs font-bold text-gray-600 focus:ring-0 resize-none">{{ old('memo', $recipe->memo) }}</textarea>
                            </div>

                        </div>
                    </div>

                    {{-- 下部アクションエリア --}}
                    <div
                        class="mt-12 pt-8 border-t border-[#EAE4DD] flex flex-col sm:flex-row items-center justify-between gap-6">

                        {{--  正しい削除URL（recipe.destroy）を指定 --}}
                        <button type="button"
                            onclick="openSharedDeleteModal('{{ route('recipe.destroy', $recipe->id) }}', '{{ $recipe->title }}')"
                            class="text-[11px] font-bold text-red-400 hover:text-red-500 hover:bg-red-50 px-4 py-3 rounded-xl transition-colors flex items-center tracking-widest w-full sm:w-auto justify-center">
                            <i class="bi bi-trash3 mr-1.5 text-sm"></i> このレシピを削除
                        </button>

                        <div class="flex items-center gap-4 w-full sm:w-auto justify-end">
                            <a href="/recipe_list"
                                class="text-[11px] font-bold text-gray-400 hover:text-gray-600 tracking-widest px-4 py-2">
                                キャンセル
                            </a>
                            <button type="submit"
                                class="bg-[#C1A173] px-10 py-3.5 rounded-full text-xs font-bold text-white shadow-md shadow-[#C1A173]/30 hover:bg-[#A88C61] hover:-translate-y-0.5 transition-all tracking-widest">
                                <i class="bi bi-check2 mr-1"></i> 変更を保存する
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- 共通パーツの削除モーダル --}}
        @include('components.delete-modal')

    </div>
@endsection

@section('js')
    {{-- モーダルを動かすためのJSの読み込み --}}
    <script src="{{ asset('js/shared_components.js') }}"></script>
@endsection
