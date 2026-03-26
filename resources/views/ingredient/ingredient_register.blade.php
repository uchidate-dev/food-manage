@extends('layouts.app')

@section('css')
    {{-- CSSの読み込み --}}
    <link rel="stylesheet" href="{{ asset('css/ingredient_register.css') }}">
@endsection

@section('content')
    <div
        class="bg-[#FAFAFA] font-sans text-gray-800 flex flex-col items-center justify-center min-h-screen p-6 relative w-full">

        {{-- 登録成功時のトースト通知 --}}
        @if (session('success'))
            <div id="toast"
                class="fixed top-8 left-1/2 transform -translate-x-1/2 -translate-y-5 opacity-0 z-50 transition-all duration-200 ease-out flex flex-col gap-4">
                <div
                    class="flex items-center bg-white border-l-4 border-[#C1A173] rounded shadow-lg px-8 py-4 min-w-[320px]">
                    <p class="text-sm font-bold text-gray-700 tracking-widest">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="w-full max-w-2xl mt-8">

            {{-- 一覧へ戻るボタン --}}
            <a href="/ingredient_list"
                class="mb-4 inline-flex items-center text-sm font-bold text-gray-400 hover:text-[#8C7A6B] transition-colors tracking-widest">
                <span class="mr-2">←</span> 一覧へ戻る
            </a>

            <div class="bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-[#FCFBFA] px-10 py-8 border-b border-gray-100">
                    <p class="mb-1 text-xs font-bold tracking-widest text-[#C1A173] uppercase">Add New Ingredient</p>
                    <h2 class="text-2xl font-bold text-gray-800"
                        style="font-family: ui-serif, Georgia, Cambria, 'Times New Roman', Times, serif;">
                        食材の登録
                    </h2>
                </div>

                <div class="px-10 py-8">
                    {{-- データの送信先(action)を設定＆Enterで入力できるのを止める --}}
                    <form action="/ingredients" method="POST" class="space-y-6" onkeydown="return event.key != 'Enter';">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-gray-500 tracking-widest mb-2 flex items-center">
                                カテゴリー <span class="text-red-400 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <select name="ingredient_cat_id" required
                                    class="w-full appearance-none bg-[#FAFAFA] border border-[#EAE4DD] rounded-md px-4 py-3 text-gray-800 font-medium focus:outline-none focus:border-[#C1A173] focus:ring-1 focus:ring-[#C1A173] transition-colors">
                                    <option value="" disabled selected>選択してください</option>
                                    <option value="1">野菜</option>
                                    <option value="2">肉</option>
                                    <option value="3">魚</option>
                                    <option value="4">貝類</option>
                                    <option value="5">麺類</option>
                                    <option value="6">米</option>
                                    <option value="7">その他</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                                    ▼</div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 tracking-widest mb-2">
                                食材名 <span class="text-red-400 ml-1">*</span></label>
                            <input type="text" name="name" required maxlength="100" placeholder="例：キャベツ、豚バラ肉"
                                class="w-full bg-[#FAFAFA] border border-[#EAE4DD] rounded-md px-4 py-3 text-gray-800 font-medium placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-1 focus:ring-[#C1A173] transition-colors">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 tracking-widest mb-2">保管場所 <span
                                    class="text-red-400 ml-1">*</span></label>
                            <div class="flex gap-4">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="storage_type" value="0" class="peer hidden" checked>
                                    <div
                                        class="text-center bg-white border border-[#EAE4DD] rounded-md px-4 py-3 font-bold text-gray-400 peer-checked:border-[#8C7A6B] peer-checked:text-[#8C7A6B] peer-checked:bg-[#FAF8F5] transition-all shadow-sm">
                                        冷蔵庫</div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="storage_type" value="1" class="peer hidden">
                                    <div
                                        class="text-center bg-white border border-[#EAE4DD] rounded-md px-4 py-3 font-bold text-gray-400 peer-checked:border-[#8C7A6B] peer-checked:text-[#8C7A6B] peer-checked:bg-[#FAF8F5] transition-all shadow-sm">
                                        冷凍庫
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div class="w-1/3">
                                <label class="block text-xs font-bold text-gray-500 tracking-widest mb-2">数量 <span
                                        class="text-red-400 ml-1">*</span></label>
                                <input type="number" name="quantity" min="0" required placeholder="例：1"
                                    class="w-full bg-[#FAFAFA] border border-[#EAE4DD] rounded-md px-4 py-3 text-gray-800 font-medium placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-1 focus:ring-[#C1A173] transition-colors">
                            </div>

                            <div class="w-2/3">
                                <label class="block text-xs font-bold text-gray-500 tracking-widest mb-2">賞味期限</label>
                                <input type="date" name="expiration_date"
                                    class="w-full bg-[#FAFAFA] border border-[#EAE4DD] rounded-md px-4 py-3 text-gray-800 font-medium focus:outline-none focus:border-[#C1A173] focus:ring-1 focus:ring-[#C1A173] transition-colors">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 tracking-widest mb-2">メモ・詳細</label>
                            <textarea name="memo" rows="3" maxlength="255" placeholder="単位(g,パック)や、使い道の予定など..."
                                class="w-full bg-[#FAFAFA] border border-[#EAE4DD] rounded-md px-4 py-3 text-gray-800 font-medium placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-1 focus:ring-[#C1A173] transition-colors resize-none"></textarea>
                        </div>

                        <div class="mt-10 flex items-center justify-end">
                            {{-- キャンセルボタン（一覧へ戻る） --}}
                            <button type="button" onclick="location.href='/ingredient_list'"
                                class="px-4 py-3 text-sm font-bold text-gray-400 hover:text-gray-600 mr-auto transition-colors tracking-widest">
                                キャンセル
                            </button>

                            {{-- 送信ボタン1（続けて登録） --}}
                            <button type="submit" name="action" value="continue"
                                class="border border-[#C1A173] text-[#C1A173] bg-white px-6 py-3 rounded-md text-sm font-bold hover:bg-[#FCFBFA] transition-all tracking-widest">続けて登録する</button>

                            {{-- 送信ボタン2（登録して一覧へ） --}}
                            <button type="submit" name="action" value="return"
                                class="bg-[#8C7A6B] px-6 py-3 rounded-md text-sm font-bold text-white shadow-md hover:bg-[#7A695A] transition-all tracking-widest ml-3">登録して一覧へ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- JSの読み込み --}}
@section('js')
    <script src="{{ asset('js/ingredient_register.js') }}"></script>
@endsection
