{{-- 共通ヘッダー --}}
@extends('layouts.app')

{{-- CSSの読み込み --}}
@section('css')
    <link rel="stylesheet" href="{{ asset('css/ingredient_list.css') }}">
@endsection

@section('content')
    <div class="bg-[#FAF9F6] font-sans text-[#4A4A4A] relative min-h-screen pb-20">

        {{-- トースト通知 --}}
        @if (session('success'))
            <div id="flash-message"
                class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 bg-[#C1A173] text-white px-8 py-3.5 rounded-full shadow-lg shadow-[#C1A173]/30 font-bold text-xs tracking-widest flex items-center transition-opacity duration-500">
                <i class="bi bi-check-circle-fill mr-2 text-lg"></i>
                {{ session('success') }}
            </div>
            {{-- ※もし public/js/flash_message.js を作っている場合は、このscriptタグは消してOKです！ --}}
            <script>
                setTimeout(() => {
                    const flash = document.getElementById('flash-message');
                    if (flash) {
                        flash.style.opacity = '0';
                        setTimeout(() => flash.remove(), 500);
                    }
                }, 3000);
            </script>
        @endif

        {{-- 画面上部 タイトル＆検索＆新規登録ボタン --}}
        <div class="px-6 py-8 md:p-12">
            <div class="mx-auto max-w-6xl">

                <div
                    class="mb-10 flex flex-col md:flex-row md:items-end justify-between border-b border-[#EAE4DD] pb-8 gap-8">
                    <div>
                        <a href="/home"
                            class="mb-3 inline-flex items-center text-[11px] font-bold text-gray-400 hover:text-[#C1A173] transition-colors tracking-widest">
                            <i class="bi bi-arrow-left mr-2"></i> ホームに戻る
                        </a>
                        <h1 class="text-3xl font-logo font-bold text-[#8C7A6B] tracking-tight">
                            Kitchen Stock
                        </h1>
                        <p class="text-[11px] text-[#C1A173] font-bold tracking-[0.1em] mt-1">
                            すべての食材一覧
                        </p>
                    </div>

                    <div class="flex flex-col md:flex-row items-start md:items-end gap-6">
                        <form action="/ingredient_list" method="GET" class="flex flex-wrap items-center gap-6 m-0">

                            {{-- カテゴリー検索 --}}
                            <div class="flex flex-col">
                                <label class="text-[10px] font-bold text-gray-400 tracking-widest mb-1"><i
                                        class="bi bi-funnel mr-1"></i>カテゴリー</label>
                                <div class="relative">
                                    <select name="category_id" onchange="this.form.submit()"
                                        class="appearance-none bg-transparent border-b-2 border-gray-200 py-1.5 pr-8 text-xs font-bold text-gray-600 hover:text-[#8C7A6B] focus:outline-none focus:border-[#C1A173] cursor-pointer transition-colors tracking-widest">
                                        <option value="">すべて</option>
                                        <option value="1" {{ $selectedCategory == '1' ? 'selected' : '' }}>🥦 野菜
                                        </option>
                                        <option value="2" {{ $selectedCategory == '2' ? 'selected' : '' }}>🥩 肉
                                        </option>
                                        <option value="3" {{ $selectedCategory == '3' ? 'selected' : '' }}>🐟 魚
                                        </option>
                                        <option value="4" {{ $selectedCategory == '4' ? 'selected' : '' }}>🥚 卵・乳製品
                                        </option>
                                        <option value="5" {{ $selectedCategory == '5' ? 'selected' : '' }}>🧈 大豆製品
                                        </option>
                                        <option value="6" {{ $selectedCategory == '6' ? 'selected' : '' }}>🍜 麺類
                                        </option>
                                        <option value="7" {{ $selectedCategory == '7' ? 'selected' : '' }}>🍚 米・粉類
                                        </option>
                                        <option value="8" {{ $selectedCategory == '8' ? 'selected' : '' }}>❄️ 冷凍食品
                                        </option>
                                        <option value="9" {{ $selectedCategory == '9' ? 'selected' : '' }}>🧺 その他
                                        </option>
                                    </select>
                                    <span
                                        class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 text-[#C1A173] text-[10px]"><i
                                            class="bi bi-chevron-down"></i></span>
                                </div>
                            </div>

                            {{-- 並び替え --}}
                            <div class="flex flex-col">
                                <label class="text-[10px] font-bold text-gray-400 tracking-widest mb-1"><i
                                        class="bi bi-sort-down mr-1"></i>並び順</label>
                                <div class="relative">
                                    <select name="sort" onchange="this.form.submit()"
                                        class="appearance-none bg-transparent border-b-2 border-gray-200 py-1.5 pr-8 text-xs font-bold text-gray-600 hover:text-[#8C7A6B] focus:outline-none focus:border-[#C1A173] cursor-pointer transition-colors tracking-widest">
                                        <option value="expiry_asc" {{ $selectedSort == 'expiry_asc' ? 'selected' : '' }}>
                                            期限が近い順</option>
                                        <option value="category_asc"
                                            {{ $selectedSort == 'category_asc' ? 'selected' : '' }}>カテゴリー順</option>
                                        <option value="created_desc"
                                            {{ $selectedSort == 'created_desc' ? 'selected' : '' }}>新着順</option>
                                    </select>
                                    <span
                                        class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 text-[#C1A173] text-[10px]"><i
                                            class="bi bi-chevron-down"></i></span>
                                </div>
                            </div>
                        </form>

                        <button onclick="location.href='/ingredient_register'"
                            class="inline-flex items-center rounded-xl bg-[#C1A173] px-6 py-3 text-xs font-bold text-white shadow-md shadow-[#C1A173]/20 transition-all hover:bg-[#A88C61] hover:-translate-y-0.5 tracking-widest whitespace-nowrap mb-0.5 group">
                            <i class="bi bi-plus-lg mr-2 group-hover:rotate-90 transition-transform"></i> 新規登録
                        </button>
                    </div>
                </div>

                {{-- 在庫一覧テーブル --}}
                <div class="overflow-x-auto rounded-2xl border border-[#EAE4DD] bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-[#EAE4DD]">
                        <thead class="bg-[#FAF9F6]">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-[11px] font-bold tracking-[0.2em] text-gray-400">カテゴリー
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-[11px] font-bold tracking-[0.2em] text-gray-400">食材名
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-[11px] font-bold tracking-[0.2em] text-gray-400">保管場所
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-[11px] font-bold tracking-[0.2em] text-gray-400">状態</th>
                                <th scope="col"
                                    class="px-6 py-4 text-center text-[11px] font-bold tracking-[0.2em] text-gray-400">数量
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-center text-[11px] font-bold tracking-[0.2em] text-gray-400">賞味期限
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-[11px] font-bold tracking-[0.2em] text-gray-400">メモ</th>
                                <th scope="col"
                                    class="px-6 py-4 text-center text-[11px] font-bold tracking-[0.2em] text-gray-400">操作
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EAE4DD] bg-white">

                            @forelse ($ingredients as $ingredient)
                                <tr class="transition-colors hover:bg-[#FFFDFB] group">

                                    {{-- カテゴリー＆アイコン --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-[#FAF9F6] flex items-center justify-center text-sm shadow-inner">
                                                {{ $ingredient['icon'] }}
                                            </div>
                                            <span
                                                class="text-xs font-bold text-gray-500 tracking-widest">{{ $ingredient['category_name'] }}</span>
                                        </div>
                                    </td>

                                    {{-- 食材名 --}}
                                    <td class="px-6 py-4 max-w-[200px] whitespace-normal break-all">
                                        <span
                                            class="text-sm font-bold text-gray-700 tracking-wider">{{ $ingredient['name'] }}</span>
                                    </td>

                                    {{-- 保管場所 --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($ingredient['storage_type'] === 0)
                                            <span
                                                class="inline-flex items-center rounded-md bg-blue-50/80 border border-blue-100 px-3 py-1 text-[10px] font-bold text-blue-500 tracking-widest">冷蔵</span>
                                        @elseif ($ingredient['storage_type'] === 1)
                                            <span
                                                class="inline-flex items-center rounded-md bg-cyan-50/80 border border-cyan-100 px-3 py-1 text-[10px] font-bold text-cyan-600 tracking-widest"><i
                                                    class="bi bi-snow mr-1 text-[9px]"></i>冷凍</span>
                                        @endif
                                    </td>

                                    {{-- 状態バッジ --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($ingredient['status_color'] === 'red')
                                            <span
                                                class="inline-flex items-center rounded-full px-3 py-1 text-[10px] font-bold text-red-500 bg-red-50 tracking-widest">{{ $ingredient['status_text'] }}</span>
                                        @elseif ($ingredient['status_color'] === 'amber')
                                            <span
                                                class="inline-flex items-center rounded-full px-3 py-1 text-[10px] font-bold text-amber-500 bg-amber-50 tracking-widest">{{ $ingredient['status_text'] }}</span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full px-3 py-1 text-[10px] font-bold text-gray-400 bg-gray-50 tracking-widest">{{ $ingredient['status_text'] }}</span>
                                        @endif
                                    </td>

                                    {{-- 数量 --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-600">{{ $ingredient['quantity'] }}</span>
                                    </td>

                                    {{-- 賞味期限 --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span
                                            class="text-xs font-mono font-bold {{ $ingredient['status_color'] === 'red' ? 'text-red-400' : 'text-gray-500' }}">
                                            {{ $ingredient['expiration_date'] ? \Carbon\Carbon::parse($ingredient['expiration_date'])->format('Y/m/d') : '-' }}
                                        </span>
                                    </td>

                                    {{-- メモ --}}
                                    <td class="px-6 py-4">
                                        <p
                                            class="max-w-[150px] text-[11px] font-medium text-gray-400 leading-relaxed truncate group-hover:whitespace-normal group-hover:bg-white transition-all">
                                            {{ $ingredient['memo'] ?: 'ー' }}
                                        </p>
                                    </td>

                                    {{-- 操作ボタン --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div
                                            class="flex items-center justify-center space-x-4 opacity-70 group-hover:opacity-100 transition-opacity">
                                            <button onclick="location.href='/ingredient_update/{{ $ingredient['id'] }}'"
                                                class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-[#C1A173] hover:bg-white shadow-sm transition-all"
                                                title="編集">
                                                <i class="bi bi-pencil-fill text-xs"></i>
                                            </button>

                                            {{--  共通の削除モーダルを呼び出す！ --}}
                                            <button
                                                onclick="openSharedDeleteModal('/ingredient_delete/{{ $ingredient['id'] }}', '{{ $ingredient['name'] }}')"
                                                class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-300 hover:text-white hover:bg-red-400 shadow-sm transition-all"
                                                title="削除">
                                                <i class="bi bi-trash3-fill text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                {{-- 空の時のメッセージ --}}
                                <tr>
                                    <td colspan="8" class="px-6 py-32 text-center bg-white">
                                        <span class="text-5xl mb-6 block opacity-50">🧺</span>

                                        @if (empty($selectedCategory))
                                            <h3 class="text-sm font-bold text-[#8C7A6B] tracking-widest mb-2">冷蔵庫はまだ空っぽです
                                            </h3>
                                            <p class="text-xs text-gray-400 font-medium tracking-widest leading-relaxed">
                                                「＋ 新規登録」ボタンから<br>最初の食材を追加してみましょう！
                                            </p>
                                        @else
                                            <h3 class="text-sm font-bold text-[#8C7A6B] tracking-widest mb-2">
                                                条件に合う食材が見つかりません</h3>
                                            <p class="text-xs text-gray-400 font-medium tracking-widest leading-relaxed">
                                                カテゴリーを変更するか、<br>新しく食材を追加してください。
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

        {{--  共通パーツの削除モーダル --}}
        @include('components.delete-modal')

    </div>
@endsection
