@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/ingredient_update.css') }}">
@endsection

@section('content')
    <div
        class="bg-[#FAFAFA] font-sans text-gray-800 flex flex-col items-center justify-center min-h-screen p-6 relative w-full">

        {{-- トースト通知 --}}
        @if (session('success'))
            <div id="toast"
                class="fixed top-8 left-1/2 transform -translate-x-1/2 -translate-y-5 opacity-0 z-50 transition-all duration-200 ease-out flex flex-col gap-4">
                <div
                    class="flex items-center bg-white border-l-4 border-[#C1A173] rounded shadow-lg px-8 py-4 min-w-[320px]">
                    <p class="text-sm font-bold text-gray-700 tracking-widest">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- 削除モーダル --}}
        <div id="deleteModal"
            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-white/80 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-lg shadow-2xl border border-gray-100 p-12 max-w-sm w-full text-center">
                <h3 class="text-xl font-bold text-gray-800 tracking-widest mb-4">本当に削除しますか？</h3>
                <p class="text-xs font-medium text-gray-400 leading-relaxed mb-10">
                    削除したデータは元に戻すことができません。<br>本当に削除してもよろしいですか？
                </p>

                {{-- 削除用の送信フォーム（今の食材のIDをセット） --}}
                <form id="deleteForm" action="{{ url('/ingredient_delete/' . $ingredient->id) }}" method="POST">
                    @csrf
                    <div class="flex gap-4">
                        <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 border border-gray-200 text-gray-500 px-4 py-3 rounded text-sm font-bold transition-colors hover:bg-gray-50 tracking-widest">
                            キャンセル
                        </button>
                        <button type="submit"
                            class="flex-1 bg-red-500 text-white px-4 py-3 rounded text-sm font-bold shadow-md transition-colors hover:bg-red-600 tracking-widest">
                            削除する
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="w-full max-w-2xl mt-8">

            {{-- 一覧へ戻るボタン --}}
            <a href="/ingredient_list"
                class="mb-4 inline-flex items-center text-sm font-bold text-gray-400 hover:text-[#8C7A6B] transition-colors tracking-widest">
                <span class="mr-2">←</span> 一覧へ戻る
            </a>

            <div class="bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-[#FCFBFA] px-10 py-8 border-b border-gray-100">
                    <p class="mb-1 text-xs font-bold tracking-widest text-[#C1A173] uppercase">Edit Ingredient</p>
                    <h2 class="text-2xl font-bold text-gray-800"
                        style="font-family: ui-serif, Georgia, Cambria, 'Times New Roman', Times, serif;">
                        食材編集
                    </h2>
                </div>

                <div class="px-10 py-8">
                    {{-- 更新用の送信フォーム --}}
                    <form action="/ingredient_update/{{ $ingredient->id }}" method="POST" class="space-y-6"
                        onkeydown="return event.key != 'Enter';">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-bold text-gray-500 tracking-widest mb-2 flex items-center">
                                カテゴリー <span class="text-red-400 ml-1">*</span>
                            </label>
                            <div class="relative">
                                {{-- 初期値(selected)を設定 --}}
                                <select name="ingredient_cat_id" required
                                    class="w-full appearance-none bg-[#FAFAFA] border border-[#EAE4DD] rounded-md px-4 py-3 text-gray-800 font-medium focus:outline-none focus:border-[#C1A173] focus:ring-1 focus:ring-[#C1A173] transition-colors">
                                    <option value="" disabled>選択してください</option>

                                    {{-- 登録されてるカテゴリーを選択状態にする --}}
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $ingredient->ingredient_cat_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach

                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                                    ▼</div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 tracking-widest mb-2">食材名 <span
                                    class="text-red-400">*</span></label>
                            {{-- 初期値(value)を設定 --}}
                            <input type="text" name="name" value="{{ $ingredient->name }}" required maxlength="100"
                                placeholder="例：キャベツ、豚バラ肉"
                                class="w-full bg-[#FAFAFA] border border-[#EAE4DD] rounded-md px-4 py-3 text-gray-800 font-medium placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-1 focus:ring-[#C1A173] transition-colors">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 tracking-widest mb-2">保管場所 <span
                                    class="text-red-400">*</span></label>
                            <div class="flex gap-4">
                                {{-- 初期値(checked)を設定 --}}
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="storage_type" value="0" class="peer hidden"
                                        {{ $ingredient->storage_type == 0 ? 'checked' : '' }}>
                                    <div
                                        class="text-center bg-white border border-[#EAE4DD] rounded-md px-4 py-3 font-bold text-gray-400 peer-checked:border-[#8C7A6B] peer-checked:text-[#8C7A6B] peer-checked:bg-[#FAF8F5] transition-all shadow-sm">
                                        冷蔵庫</div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="storage_type" value="1" class="peer hidden"
                                        {{ $ingredient->storage_type == 1 ? 'checked' : '' }}>
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
                                        class="text-red-400">*</span></label>
                                {{-- 初期値(value)を設定 --}}
                                <input type="number" name="quantity" value="{{ $ingredient->quantity }}" min="0"
                                    required placeholder="例：1"
                                    class="w-full bg-[#FAFAFA] border border-[#EAE4DD] rounded-md px-4 py-3 text-gray-800 font-medium placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-1 focus:ring-[#C1A173] transition-colors">
                            </div>

                            <div class="w-2/3">
                                <label class="block text-xs font-bold text-gray-500 tracking-widest mb-2">賞味期限</label>
                                {{-- 初期値(value)を設定 --}}
                                <input type="date" name="expiration_date" value="{{ $ingredient->expiration_date }}"
                                    class="w-full bg-[#FAFAFA] border border-[#EAE4DD] rounded-md px-4 py-3 text-gray-800 font-medium focus:outline-none focus:border-[#C1A173] focus:ring-1 focus:ring-[#C1A173] transition-colors">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 tracking-widest mb-2">メモ・詳細</label>
                            {{-- 初期値(タグの間に中身)を設定 --}}
                            <textarea name="memo" rows="3" maxlength="255" placeholder="単位(g,パック)や、使い道の予定など..."
                                class="w-full bg-[#FAFAFA] border border-[#EAE4DD] rounded-md px-4 py-3 text-gray-800 font-medium placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-1 focus:ring-[#C1A173] transition-colors resize-none">{{ $ingredient->memo }}</textarea>
                        </div>

                        {{-- ボタンエリア --}}
                        <div class="mt-10 flex items-center justify-between">
                            {{-- 削除するボタン --}}
                            <button type="button" onclick="openDeleteModal()"
                                class="text-sm font-bold text-red-400 hover:text-red-500 transition-colors tracking-widest">
                                削除する
                            </button>

                            {{-- キャンセル ＆ 更新するボタン --}}
                            <div class="flex items-center">
                                <button type="button" onclick="location.href='/ingredient_list'"
                                    class="px-6 py-3 text-sm font-bold text-gray-800 hover:text-gray-500 transition-colors mr-2 tracking-widest">
                                    キャンセル</button>
                                <button type="submit"
                                    class="bg-[#8C7A6B] px-8 py-3 rounded-md text-sm font-bold text-white shadow-md hover:bg-[#7A695A] transition-all tracking-widest">更新する
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

{{-- JSの処理（モーダルの開閉とトースト） --}}
@section('js')
    <script src="{{ asset('js/ingredient_update.js') }}"></script>
@endsection
