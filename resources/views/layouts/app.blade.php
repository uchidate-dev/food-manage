<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>My Kitchen Stock</title>
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text x=%2250%%22 y=%2250%%22 style=%22dominant-baseline:central;text-anchor:middle;font-size:90px;%22>🍳</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Quicksand:wght@500;600&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    @yield('css')
</head>

<body class="bg-[#FAF9F6] text-[#4A4A4A]">
    <div id="app">
        <nav class="bg-white border-b border-[#EAE4DD] shadow-sm relative z-50">
            <div class="container mx-auto px-4 py-3 md:px-8 flex justify-between items-center">

                {{-- ロゴ --}}
                <a class="text-xl md:text-2xl font-logo font-semibold tracking-tight text-[#8C7A6B] hover:opacity-70 transition-opacity"
                    href="{{ auth()->check() ? url('/home') : url('/login') }}">
                    My Kitchen <span class="text-[#C1A173]">Stock</span>
                </a>

                {{-- ナビゲーション --}}
                <div class="flex items-center gap-4 md:gap-8">
                    @auth
                        <div class="hidden sm:flex gap-6 text-[13px] font-bold text-[#8C7A6B] nav-link-spacing">
                            <a href="{{ url('/home') }}"
                                class="hover:text-[#C1A173] transition-colors border-b-2 border-transparent hover:border-[#C1A173] pb-1">ホーム</a>
                            <a href="{{ url('/ingredient_list') }}"
                                class="hover:text-[#C1A173] transition-colors border-b-2 border-transparent hover:border-[#C1A173] pb-1">食材管理</a>
                            <a href="{{ url('/recipe_list') }}"
                                class="hover:text-[#C1A173] transition-colors border-b-2 border-transparent hover:border-[#C1A173] pb-1">レシピ帳</a>
                        </div>

                        {{-- ログアウト --}}
                        <a href="{{ route('logout') }}"
                            class="text-[11px] font-bold tracking-widest text-[#8C7A6B] border border-[#EAE4DD] px-3 py-1.5 rounded hover:bg-[#FAF9F6] transition-all"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            ログアウト
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>

                        <button type="button"
                            onclick="document.getElementById('mobile-menu').classList.toggle('hidden'); this.querySelector('i').classList.toggle('bi-list'); this.querySelector('i').classList.toggle('bi-x-lg');"
                            class="sm:hidden text-[#8C7A6B] text-2xl focus:outline-none hover:text-[#C1A173] transition-colors">
                            <i class="bi bi-list"></i>
                        </button>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-bold text-[#8C7A6B] hover:opacity-70">ログイン</a>
                        <a href="{{ route('user_register') }}"
                            class="ml-4 text-xs font-bold text-white bg-[#C1A173] px-4 py-2 rounded-full hover:bg-[#B69666] shadow-sm transition-all">はじめる</a>
                    @endguest
                </div>
            </div>

            @auth
                <div id="mobile-menu"
                    class="hidden sm:hidden border-t border-[#EAE4DD] bg-white absolute w-full shadow-lg transition-all">
                    <div class="flex flex-col px-6 py-4 space-y-4 text-[13px] font-bold text-[#8C7A6B]">
                        <a href="{{ url('/home') }}" class="hover:text-[#C1A173] flex items-center transition-colors">
                            <i class="bi bi-house-door-fill mr-3 text-lg text-[#C1A173]"></i>ホーム
                        </a>
                        <a href="{{ url('/ingredient_list') }}"
                            class="hover:text-[#C1A173] flex items-center transition-colors">
                            <i class="bi bi-basket-fill mr-3 text-lg text-[#C1A173]"></i>食材管理
                        </a>
                        <a href="{{ url('/recipe_list') }}"
                            class="hover:text-[#C1A173] flex items-center transition-colors">
                            <i class="bi bi-journal-text mr-3 text-lg text-[#C1A173]"></i>レシピ帳
                        </a>
                    </div>
                </div>
            @endauth
        </nav>

        {{-- --- メインコンテンツ --- --}}
        <main class="py-6">
            @yield('content')
        </main>

        @yield('js')
    </div>

    <script src="{{ asset('js/shared_components.js?v=' . time()) }}"></script>
</body>

</html>
