{{-- 共通ヘッダー --}}
@extends('layouts.app')

{{-- CSSの読み込み --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('css/ingredient_list.css') }}">
@endsection

@section('content')
    <div class="bg-[#F8F8F8] font-sans text-gray-800 relative min-h-screen">

        {{-- トースト通知 --}}
        @if (session('success'))
            <div id="toast"
                class="fixed top-8 left-1/2 transform -translate-x-1/2 -translate-y-5 opacity-0 z-50 transition-all duration-500 ease-out flex flex-col gap-4">
                <div
                    class="flex items-center bg-white border-l-4 border-[#C1A173] rounded shadow-lg px-8 py-4 min-w-[320px]">
                    {{-- Controllerから送られてきた文字(登録しました等) をここに表示 --}}
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
                {{-- データ送信用のForm --}}
                <form id="deleteForm" method="POST" action="">
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

        {{-- 画面上部 タイトル＆検索＆新規登録ボタン --}}
        <div class="p-8 bg-[#F8F8F8]">
            <div class="mx-auto max-w-7xl">

                <div
                    class="mb-10 flex flex-col md:flex-row md:items-end justify-between border-b border-gray-200 pb-8 gap-6">
                    <div>
                        {{-- ホーム画面へ戻るリンク --}}
                        <a href="/home"
                            class="mb-2 inline-flex items-center text-sm font-bold text-gray-400 hover:text-[#8C7A6B] transition-colors tracking-widest">
                            <span class="mr-2">←</span>ホームへ戻る
                        </a>
                        <h1 class="text-3xl font-bold text-gray-800"
                            style="font-family: ui-serif, Georgia, Cambria, 'Times New Roman', Times, serif;">
                            My Kitchen Stock
                        </h1>
                    </div>

                    <div class="flex items-end gap-6">
                        <form action="/ingredient_list" method="GET" class="flex items-center gap-6 m-0">

                            {{-- カテゴリー検索 --}}
                            <div class="flex flex-col">
                                <label class="text-[10px] font-bold text-gray-400 tracking-widest mb-1">カテゴリー</label>
                                <div class="relative">
                                    <select name="category_id" onchange="this.form.submit()"
                                        class="appearance-none bg-transparent border-b-2 border-gray-200 py-1.5 pr-8 text-sm font-medium text-gray-600 hover:text-[#8C7A6B] focus:outline-none focus:border-[#8C7A6B] cursor-pointer transition-colors tracking-widest">
                                        <option value="">すべて</option>
                                        <option value="1" {{ $selectedCategory == '1' ? 'selected' : '' }}>🥦 野菜
                                        </option>
                                        <option value="2" {{ $selectedCategory == '2' ? 'selected' : '' }}>🥩 肉
                                        </option>
                                        <option value="3" {{ $selectedCategory == '3' ? 'selected' : '' }}>🐟 魚
                                        </option>
                                        <option value="4" {{ $selectedCategory == '4' ? 'selected' : '' }}>🦪 貝類
                                        </option>
                                        <option value="5" {{ $selectedCategory == '5' ? 'selected' : '' }}>🍜 麺類
                                        </option>
                                        <option value="6" {{ $selectedCategory == '6' ? 'selected' : '' }}>🍚 米
                                        </option>
                                        <option value="7" {{ $selectedCategory == '7' ? 'selected' : '' }}>🧺 その他
                                        </option>
                                    </select>
                                    <span
                                        class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 text-gray-300 text-xs">▼</span>
                                </div>
                            </div>

                            {{-- 並び替え --}}
                            <div class="flex flex-col">

                                <label class="text-[10px] font-bold text-gray-400 tracking-widest mb-1">並び順</label>
                                <div class="relative">
                                    <select name="sort" onchange="this.form.submit()"
                                        class="appearance-none bg-transparent border-b-2 border-gray-200 py-1 pr-8 text-sm font-bold text-gray-600 hover:text-[#8C7A6B] focus:outline-none focus:border-[#8C7A6B] cursor-pointer transition-colors tracking-widest">
                                        <option value="expiry_asc" {{ $selectedSort == 'expiry_asc' ? 'selected' : '' }}>
                                            期限が近い順</option>
                                        <option value="category_asc"
                                            {{ $selectedSort == 'category_asc' ? 'selected' : '' }}>
                                            カテゴリー順</option>
                                        <option value="created_desc"
                                            {{ $selectedSort == 'created_desc' ? 'selected' : '' }}>
                                            新着順</option>
                                    </select>
                                    <span
                                        class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 text-gray-300 text-xs">▼</span>
                                </div>
                            </div>
                        </form>

                        <button onclick="location.href='/ingredient_register'"
                            class="inline-flex items-center rounded bg-[#8C7A6B] px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-[#7A695A] tracking-widest whitespace-nowrap mb-0.5">
                            ＋ 新規登録
                        </button>
                    </div>
                </div>

                {{-- 在庫一覧テーブル --}}
                <div class="overflow-hidden rounded-lg border border-gray-100 bg-white shadow-xl">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-[#FCFBFA]">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold tracking-widest text-gray-400">カテゴリー
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold tracking-widest text-gray-400">食材名</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold tracking-widest text-gray-400">保管場所
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold tracking-widest text-gray-400">状態</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-bold tracking-widest text-gray-400">数量
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-bold tracking-widest text-gray-400">賞味期限
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold tracking-widest text-gray-400">メモ</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-bold tracking-widest text-gray-400">操作
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">

                            {{-- Controllerの情報を引っ張ってくる --}}
                            @forelse ($ingredients as $ingredient)
                                <tr class="transition-colors hover:bg-[#FAF8F5]">

                                    {{-- カテゴリー＆アイコン --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="flex items-cßenter gap-2 text-sm font-bold text-gray-600 tracking-widest">
                                            <span class="text-lg">{{ $ingredient['icon'] }}</span>
                                            {{ $ingredient['category_name'] }}
                                        </span>
                                    </td>

                                    {{-- 食材名 --}}
                                    <td class="px-6 py-4 max-w-[200px] whitespace-normal break-all">
                                        <span
                                            class="text-lg font-black text-gray-800 tracking-wider">{{ $ingredient['name'] }}</span>
                                    </td>

                                    {{-- 保管場所 (0:冷蔵庫, 1:冷凍庫で色を変える条件分岐) --}}
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        @if ($ingredient['storage_type'] === 0)
                                            <span
                                                class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-500">冷蔵庫</span>
                                        @elseif ($ingredient['storage_type'] === 1)
                                            <span
                                                class="inline-flex items-center rounded-full bg-[#F5F2EF] px-3 py-1 text-xs font-bold text-[#8C7A6B]">冷凍庫</span>
                                        @endif
                                    </td>

                                    {{-- 状態(色に合わせてデザインを変える条件分岐) --}}
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        @if ($ingredient['status_color'] === 'red')
                                            <span
                                                class="inline-flex items-center rounded border border-red-200 px-4 py-1.5 text-xs font-bold text-red-600 shadow-sm bg-red-50">{{ $ingredient['status_text'] }}</span>
                                        @elseif ($ingredient['status_color'] === 'amber')
                                            <span
                                                class="inline-flex items-center rounded border border-amber-200 px-4 py-1.5 text-xs font-bold text-amber-600 shadow-sm bg-amber-50">{{ $ingredient['status_text'] }}</span>
                                        @else
                                            <span
                                                class="text-sm font-bold text-gray-400 tracking-widest">{{ $ingredient['status_text'] }}</span>
                                        @endif
                                    </td>

                                    {{-- 数量 --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span class="text-sm font-black text-gray-800">{{ $ingredient['quantity'] }}</span>
                                    </td>

                                    {{-- 賞味期限 --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span
                                            class="text-sm font-black {{ $ingredient['status_color'] === 'red' ? 'text-red-500' : 'text-gray-800' }}">
                                            {{ $ingredient['expiration_date'] ? str_replace('-', '.', $ingredient['expiration_date']) : '-' }}
                                        </span>
                                    </td>

                                    {{-- メモ --}}
                                    <td class="px-6 py-4">
                                        <p class="max-w-xs text-xs font-medium text-gray-400 leading-relaxed break-all">
                                            {{ $ingredient['memo'] }}</p>
                                    </td>

                                    {{-- 操作ボタン --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center space-x-6">
                                            <button onclick="location.href='/ingredient_update/{{ $ingredient['id'] }}'"
                                                class="text-xs font-bold text-gray-400 transition-colors hover:text-[#C1A173] tracking-widest">編集</button>
                                            {{-- 削除ボタンにIDを渡す --}}
                                            <button onclick="openDeleteModal({{ $ingredient['id'] }})"
                                                class="text-xs font-bold text-gray-400 transition-colors hover:text-red-400 tracking-widest">削除</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                {{-- 空の時のメッセージ --}}
                                <tr>
                                    <td colspan="8" class="px-6 py-20 text-center bg-[#FCFBFA]">
                                        <span class="text-4xl mb-4 block">🧺</span>

                                        @if (empty($selectedCategory))
                                            {{-- まだ何も登録していない時 --}}
                                            <h3 class="text-lg font-bold text-gray-700 tracking-widest mb-2">冷蔵庫はまだ空です
                                            </h3>
                                            <p class="text-sm text-gray-400 font-medium tracking-widest leading-relaxed">
                                                「＋ 新規登録」ボタンから<br>
                                                最初の食材を追加してください
                                            </p>
                                        @else
                                            {{-- カテゴリーで絞り込んだ結果、見つからなかった時 --}}
                                            <h3 class="text-lg font-bold text-gray-700 tracking-widest mb-2">
                                                条件に合う食材がありません</h3>
                                            <p class="text-sm text-gray-400 font-medium tracking-widest leading-relaxed">
                                                カテゴリーを変更するか、<br>「＋ 新規登録」から追加してください
                                            </p>
                                        @endif

                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/ingredient_list.js') }}"></script>
@endsection
