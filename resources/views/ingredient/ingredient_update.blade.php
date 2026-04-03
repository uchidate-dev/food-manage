@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/ingredient_update.css') }}">
@endsection

@section('content')
    <div
        class="bg-[#FAF9F6] font-sans text-[#4A4A4A] flex flex-col items-center justify-center min-h-screen py-12 px-4 sm:px-6 relative w-full">

        <div class="w-full max-w-xl">

            {{-- 一覧へ戻るリンク --}}
            <a href="/ingredient_list"
                class="mb-6 inline-flex items-center text-[11px] font-bold text-gray-400 hover:text-[#C1A173] transition-colors tracking-widest uppercase">
                <i class="bi bi-arrow-left mr-2"></i> 一覧に戻る
            </a>

            <div class="bg-white rounded-2xl shadow-sm border border-[#EAE4DD] overflow-hidden">

                {{--  タイトルエリア --}}
                <div class="bg-[#FCFBFA] px-8 py-8 border-b border-[#EAE4DD] text-center">
                    <h2 class="text-2xl font-logo font-bold text-[#8C7A6B] tracking-tight mb-1">
                        Kitchen Stock
                    </h2>
                    <p class="text-[11px] font-bold tracking-[0.2em] text-[#C1A173]">食材の情報を編集</p>
                </div>

                <div class="px-8 py-10">
                    {{-- 更新用の送信フォーム --}}
                    <form action="/ingredient_update/{{ $ingredient->id }}" method="POST" class="space-y-8"
                        onkeydown="return event.key != 'Enter';">
                        @csrf
                        @method('PUT')

                        {{-- カテゴリー --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                <i class="bi bi-tag mr-2 text-[#C1A173]"></i>カテゴリー <span class="text-red-400 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <select name="ingredient_cat_id" required
                                    class="w-full appearance-none bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all cursor-pointer">
                                    <option value="" disabled>選択してください</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $ingredient->ingredient_cat_id == $category->id ? 'selected' : '' }}>
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

                        {{-- 食材名 --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                <i class="bi bi-pencil mr-2 text-[#C1A173]"></i>食材名 <span class="text-red-400 ml-1">*</span>
                            </label>
                            <input type="text" name="name" value="{{ $ingredient->name }}" required maxlength="100"
                                placeholder="例：キャベツ、豚バラ肉"
                                class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all">
                        </div>

                        {{-- 保管場所 --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                <i class="bi bi-box-seam mr-2 text-[#C1A173]"></i>保管場所 <span
                                    class="text-red-400 ml-1">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="storage_type" value="0" class="peer hidden"
                                        {{ $ingredient->storage_type == 0 ? 'checked' : '' }}>
                                    <div
                                        class="text-center bg-white border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-xs font-bold text-gray-400 peer-checked:border-blue-300 peer-checked:text-blue-500 peer-checked:bg-blue-50/50 transition-all shadow-sm">
                                        <i class="bi bi-snow mr-1"></i> 冷蔵
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="storage_type" value="1" class="peer hidden"
                                        {{ $ingredient->storage_type == 1 ? 'checked' : '' }}>
                                    <div
                                        class="text-center bg-white border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-xs font-bold text-gray-400 peer-checked:border-cyan-300 peer-checked:text-cyan-600 peer-checked:bg-cyan-50/50 transition-all shadow-sm">
                                        <i class="bi bi-thermometer-snow mr-1"></i> 冷凍
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- 数量 ＆ 賞味期限 --}}
                        <div class="flex gap-6">
                            <div class="w-1/3">
                                <label
                                    class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                    <i class="bi bi-123 mr-2 text-[#C1A173]"></i>数量 <span class="text-red-400 ml-1">*</span>
                                </label>
                                <input type="number" name="quantity" value="{{ $ingredient->quantity }}" min="0"
                                    required placeholder="例：1"
                                    class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all text-center">
                            </div>

                            <div class="w-2/3">
                                <label
                                    class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                    <i class="bi bi-calendar-event mr-2 text-[#C1A173]"></i>賞味期限
                                </label>
                                <input type="date" name="expiration_date" value="{{ $ingredient->expiration_date }}"
                                    class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all">
                            </div>
                        </div>

                        {{-- メモ --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                <i class="bi bi-journal-text mr-2 text-[#C1A173]"></i>メモ・詳細
                            </label>
                            <textarea name="memo" rows="3" maxlength="255" placeholder="単位(g,パック)や、使い道の予定など..."
                                class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-medium text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all resize-none">{{ $ingredient->memo }}</textarea>
                        </div>

                        {{-- ボタンエリア --}}
                        <div
                            class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-100">

                            {{-- 共通の削除モーダル --}}
                            <button type="button"
                                onclick="openSharedDeleteModal('/ingredient_delete/{{ $ingredient->id }}', '{{ $ingredient->name }}')"
                                class="w-full sm:w-auto flex justify-center items-center text-[11px] font-bold text-red-400 hover:text-red-500 hover:bg-red-50 px-4 py-3 rounded-xl transition-all tracking-widest">
                                <i class="bi bi-trash3 mr-1.5 text-sm"></i> 削除する
                            </button>

                            {{-- キャンセル ＆ 更新ボタン（右側） --}}
                            <div class="flex items-center gap-3 w-full sm:w-auto">
                                <button type="button" onclick="location.href='/ingredient_list'"
                                    class="w-full sm:w-auto px-6 py-3.5 text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors tracking-widest">
                                    キャンセル
                                </button>
                                <button type="submit"
                                    class="w-full sm:w-auto bg-[#8C7A6B] px-8 py-3.5 rounded-xl text-xs font-bold text-white shadow-md shadow-[#8C7A6B]/30 hover:bg-[#7A695A] hover:-translate-y-0.5 transition-all tracking-widest">
                                    <i class="bi bi-check2 mr-1"></i> 更新する
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{--  共通パーツの削除モーダルの呼び出し --}}
        @include('components.delete-modal')

    </div>
@endsection
