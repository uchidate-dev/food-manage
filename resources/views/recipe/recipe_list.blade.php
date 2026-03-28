@extends('layouts.app')

@section('content')

    @php
        // ユーザーごとのお気に入りID一覧（N+1回避）
        $favoriteIds = [];
        if (auth()->check()) {
            $favoriteIds = auth()->user()->favoriteRecipes()->pluck('recipes.id')->toArray();
        }
    @endphp

    <div class="bg-[#FAF9F6] font-sans text-[#4A4A4A] min-h-screen pb-20">
        <div class="max-w-7xl mx-auto px-6 py-8 md:p-12">

            {{-- 👑 ヘッダーエリア --}}
            <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between border-b border-[#EAE4DD] pb-8 gap-6">
                <div>
                    {{-- Dashboardへ戻るリンク --}}
                    <a href="/home"
                        class="mb-3 inline-flex items-center text-[11px] font-bold text-gray-400 hover:text-[#C1A173] transition-colors tracking-widest uppercase">
                        <i class="bi bi-arrow-left mr-2"></i> ← ホームに戻る
                    </a>
                    <h1 class="text-3xl font-logo font-bold text-[#8C7A6B] tracking-tight">
                        My Recipe Book
                    </h1>
                    <p class="text-[11px] text-[#C1A173] font-bold tracking-[0.1em] mt-1">
                        あなただけのレシピコレクション
                    </p>
                </div>

                {{-- AI機能への導線ボタン --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('recipe.suggest') }}"
                        class="inline-flex items-center rounded-xl bg-[#C1A173] px-6 py-3 text-xs font-bold text-white shadow-md shadow-[#C1A173]/20 transition-all hover:bg-[#A88C61] hover:-translate-y-0.5 tracking-widest whitespace-nowrap group">
                        <i class="bi bi-stars mr-2 group-hover:rotate-12 transition-transform"></i> 新しい献立をAIに提案してもらう
                    </a>
                </div>
            </div>

            {{-- 👑 メインコンテンツエリア --}}
            <div class="grid grid-cols-12 gap-8">

                {{-- 👈 左サイドバー：フィルタ＆検索 --}}
                <aside class="col-span-12 lg:col-span-3 space-y-6">

                    {{-- 絞り込みカード --}}
                    <div class="rounded-2xl border border-[#EAE4DD] bg-white p-6 shadow-sm">

                        <div class="mb-6">
                            <a href="{{ route('recipe.list', array_merge(request()->query(), ['fav' => $fav ? 0 : 1, 'page' => null])) }}"
                                class="flex justify-center items-center gap-2 rounded-xl border px-4 py-3.5 text-xs font-bold transition-all tracking-widest
                                {{ $fav ? 'bg-red-50 border-red-100 text-red-400 shadow-inner' : 'bg-white border-[#EAE4DD] text-gray-500 hover:bg-[#FAF9F6]' }}">
                                <i class="bi {{ $fav ? 'bi-heart-fill' : 'bi-heart' }} text-sm"></i>
                                お気に入りだけ表示
                            </a>
                        </div>

                        <div class="border-t border-[#EAE4DD] pt-6">
                            <div class="text-[11px] font-bold text-gray-400 mb-3 tracking-widest"><i
                                    class="bi bi-tags mr-1"></i> カテゴリ</div>
                            <div class="space-y-1">
                                <a href="{{ route('recipe.list', array_merge(request()->query(), ['cat' => null, 'page' => null])) }}"
                                    class="block rounded-lg px-3 py-2.5 text-xs font-bold tracking-widest transition-colors
                                    {{ empty($cat) ? 'bg-[#F5F2EF] text-[#8C7A6B]' : 'text-gray-500 hover:bg-[#FAF9F6]' }}">
                                    すべて
                                </a>
                                @foreach ($categories as $c)
                                    <a href="{{ route('recipe.list', array_merge(request()->query(), ['cat' => $c->id, 'page' => null])) }}"
                                        class="block rounded-lg px-3 py-2.5 text-xs font-bold tracking-widest transition-colors
                                        {{ (string) $cat === (string) $c->id ? 'bg-[#F5F2EF] text-[#8C7A6B]' : 'text-gray-500 hover:bg-[#FAF9F6]' }}">
                                        {{ $c->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- 検索フォーム --}}
                        <div class="border-t border-[#EAE4DD] pt-6 mt-6">
                            <div class="text-[11px] font-bold text-gray-400 mb-3 tracking-widest"><i
                                    class="bi bi-search mr-1"></i> キーワード検索</div>
                            <form method="GET" action="{{ route('recipe.list') }}" class="space-y-3">
                                <input type="hidden" name="cat" value="{{ $cat }}">
                                <input type="hidden" name="fav" value="{{ $fav ? 1 : 0 }}">

                                <input name="q" value="{{ $q }}" placeholder="タイトル・材料..."
                                    class="w-full bg-[#FAF9F6] rounded-xl border border-[#EAE4DD] px-4 py-3 text-xs font-bold text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all">

                                <button type="submit"
                                    class="w-full rounded-xl bg-[#4A3F35] text-white px-4 py-3 text-xs font-bold tracking-widest hover:bg-[#333333] transition-colors shadow-md">
                                    検索する
                                </button>

                                @if ($q !== '')
                                    <a href="{{ route('recipe.list', array_merge(request()->query(), ['q' => null, 'page' => null])) }}"
                                        class="block text-center rounded-xl border border-gray-200 px-4 py-2.5 text-[10px] font-bold text-gray-400 hover:bg-gray-50 tracking-widest transition-colors mt-2">
                                        検索条件をクリア
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    {{-- 最近追加したレシピ --}}
                    <div class="rounded-2xl border border-[#EAE4DD] bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-center gap-2 mb-6">
                            <i class="bi bi-clock-history text-[#C1A173]"></i>
                            <span class="text-xs font-bold text-[#8C7A6B] tracking-widest uppercase">最近のレシピ</span>
                        </div>

                        @if ($latestRecipes->isEmpty())
                            <div class="text-[11px] font-bold text-gray-400 text-center py-4 tracking-widest">
                                追加されたレシピは<br>まだありません
                            </div>
                        @else
                            <ul class="space-y-4">
                                @foreach ($latestRecipes as $r)
                                    <li class="group">
                                        <a href="{{ route('recipe.detail', ['id' => $r->id, 'back' => url()->full()]) }}"
                                            class="block text-xs font-bold text-gray-700 group-hover:text-[#C1A173] transition-colors leading-relaxed">
                                            {{ $r->title }}
                                        </a>
                                        <div class="text-[9px] font-bold text-gray-400 mt-1 tracking-widest uppercase">
                                            {{ optional($r->category)->name ?? '未分類' }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </aside>

                {{-- 👉 右メインエリア：レシピ一覧 --}}
                <main class="col-span-12 lg:col-span-9">
                    <div class="rounded-2xl border border-[#EAE4DD] bg-white shadow-sm overflow-hidden">

                        <div class="px-6 py-5 border-b border-[#EAE4DD] flex items-center justify-between bg-[#FAF9F6]/50">
                            <div class="text-[11px] font-bold text-gray-400 tracking-widest">
                                全 <span class="text-[#8C7A6B] text-sm">{{ $recipes->total() }}</span> 件のレシピ
                            </div>
                        </div>

                        @if ($recipes->count() === 0)
                            <div class="py-32 text-center flex flex-col items-center justify-center">
                                <i class="bi bi-journal-x text-4xl text-gray-200 mb-4"></i>
                                <p class="text-sm font-bold text-gray-400 tracking-widest">条件に合うレシピが見つかりません</p>
                            </div>
                        @endif

                        <div class="divide-y divide-[#EAE4DD]">
                            @foreach ($recipes as $recipe)
                                @php
                                    $isFavorited = in_array($recipe->id, $favoriteIds, true);
                                @endphp

                                <div
                                    class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 hover:bg-[#FFFDFB] transition-colors group">

                                    {{-- レシピ情報 --}}
                                    <div class="flex-1 min-w-0 w-full">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span
                                                class="inline-flex items-center rounded-md bg-[#F5F2EF] px-2 py-0.5 text-[10px] font-bold text-[#8C7A6B] tracking-widest">
                                                {{ optional($recipe->category)->name ?? '未分類' }}
                                            </span>
                                        </div>

                                        <a href="{{ route('recipe.detail', ['id' => $recipe->id, 'back' => url()->full()]) }}"
                                            class="block text-lg font-bold text-gray-700 truncate group-hover:text-[#C1A173] transition-colors">
                                            {{ $recipe->title }}
                                        </a>
                                    </div>

                                    {{-- アクションボタン --}}
                                    <div class="flex items-center justify-end w-full sm:w-auto gap-4 shrink-0">
                                        {{-- お気に入りボタン（JavaScriptで動く） --}}
                                        <button type="button"
                                            class="favorite-btn text-xl transition-transform hover:scale-110 {{ $isFavorited ? 'text-red-400' : 'text-gray-300' }}"
                                            data-id="{{ $recipe->id }}">
                                            <i class="bi {{ $isFavorited ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                        </button>

                                        {{-- 詳細ボタン --}}
                                        <a href="{{ route('recipe.detail', ['id' => $recipe->id, 'back' => url()->full()]) }}"
                                            class="w-10 h-10 rounded-full bg-[#FAF9F6] flex items-center justify-center text-gray-400 group-hover:bg-[#C1A173] group-hover:text-white shadow-sm transition-all"
                                            title="詳細を見る">
                                            <i class="bi bi-chevron-right text-sm"></i>
                                        </a>
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        {{-- ページネーション --}}
                        <div class="px-6 py-5 border-t border-[#EAE4DD] bg-[#FAF9F6]/50">
                            {{ $recipes->links() }}
                        </div>

                    </div>
                </main>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/recipe_favorite.js') }}"></script>
@endsection
