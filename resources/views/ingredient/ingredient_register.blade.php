@extends('layouts.app')

@section('css')
    {{-- CSSの読み込み --}}
    <link rel="stylesheet" href="{{ asset('css/ingredient_register.css') }}">
@endsection

@section('content')
    <div
        class="bg-[#FAF9F6] font-sans text-[#4A4A4A] flex flex-col items-center justify-center min-h-screen py-12 px-4 sm:px-6 relative w-full">

        {{-- 登録成功時のトースト通知 --}}
        @if (session('success'))
            <div id="flash-message"
                class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 bg-[#C1A173] text-white px-8 py-3.5 rounded-full shadow-lg shadow-[#C1A173]/30 font-bold text-xs tracking-widest flex items-center transition-opacity duration-500">
                <i class="bi bi-check-circle-fill mr-2 text-lg"></i>
                {{ session('success') }}
            </div>
        @endif


        <div class="w-full max-w-xl">

            {{-- 一覧へ戻るリンク --}}
            <a href="/ingredient_list"
                class="mb-6 inline-flex items-center text-[11px] font-bold text-gray-400 hover:text-[#C1A173] transition-colors tracking-widest uppercase">
                <i class="bi bi-arrow-left mr-2"></i> 一覧に戻る
            </a>

            <div class="bg-white rounded-2xl shadow-sm border border-[#EAE4DD] overflow-hidden">

                {{-- タイトルエリア --}}
                <div class="bg-[#FCFBFA] px-8 py-8 border-b border-[#EAE4DD] text-center">
                    <h2 class="text-2xl font-logo font-bold text-[#8C7A6B] tracking-tight mb-1">
                        Kitchen Stock
                    </h2>
                    <p class="text-[11px] font-bold tracking-[0.2em] text-[#C1A173]">新しい食材を登録</p>
                </div>

                <div class="px-8 py-10">
                    {{-- データの送信先(action)を設定＆Enterで入力できるのを止める --}}
                    <form action="/ingredients" method="POST" class="space-y-8" onkeydown="return event.key != 'Enter';">
                        @csrf

                        {{-- カテゴリー --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                <i class="bi bi-tag mr-2 text-[#C1A173]"></i>カテゴリー <span class="text-red-400 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <select name="ingredient_cat_id" required
                                    class="w-full appearance-none bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all cursor-pointer">
                                    <option value="" disabled selected>選択してください</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                            <input type="text" name="name" required maxlength="100" placeholder="例：キャベツ、豚バラ肉"
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
                                    <input type="radio" name="storage_type" value="0" class="peer hidden" checked>
                                    <div
                                        class="text-center bg-white border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-xs font-bold text-gray-400 peer-checked:border-blue-300 peer-checked:text-blue-500 peer-checked:bg-blue-50/50 transition-all shadow-sm">
                                        <i class="bi bi-snow mr-1"></i> 冷蔵
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="storage_type" value="1" class="peer hidden">
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
                                <input type="number" name="quantity" min="0" required placeholder="例：1"
                                    class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all text-center">
                            </div>

                            <div class="w-2/3">
                                <label
                                    class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                    <i class="bi bi-calendar-event mr-2 text-[#C1A173]"></i>賞味期限
                                </label>
                                <input type="date" name="expiration_date"
                                    class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all">
                            </div>
                        </div>

                        {{-- メモ --}}
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2 flex items-center">
                                <i class="bi bi-journal-text mr-2 text-[#C1A173]"></i>メモ・詳細
                            </label>
                            <textarea name="memo" rows="3" maxlength="255" placeholder="単位(g,パック)や、使い道の予定など..."
                                class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-medium text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all resize-none"></textarea>
                        </div>

                        {{-- ボタンエリア --}}
                        <div
                            class="mt-12 flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t border-gray-50">
                            {{-- キャンセルボタン（一覧へ戻る） --}}
                            <button type="button" onclick="location.href='/ingredient_list'"
                                class="w-full sm:w-auto px-6 py-3.5 text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors tracking-widest sm:mr-auto">
                                キャンセル
                            </button>

                            {{-- 送信ボタン1（続けて登録） --}}
                            <button type="submit" name="action" value="continue"
                                class="w-full sm:w-auto border-2 border-[#C1A173] text-[#C1A173] bg-white px-6 py-3 rounded-xl text-xs font-bold hover:bg-[#FAF9F6] transition-all tracking-widest">
                                続けて登録する
                            </button>

                            {{-- 送信ボタン2（登録して一覧へ） --}}
                            <button type="submit" name="action" value="return"
                                class="w-full sm:w-auto bg-[#8C7A6B] px-8 py-3.5 rounded-xl text-xs font-bold text-white shadow-md shadow-[#8C7A6B]/30 hover:bg-[#7A695A] hover:-translate-y-0.5 transition-all tracking-widest">
                                <i class="bi bi-check2 mr-1"></i> 登録して一覧へ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
