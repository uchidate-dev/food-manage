<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>My Kitchen Stock</title>

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
        <nav class="bg-white border-b border-[#EAE4DD] py-3 shadow-sm">
            <div class="container mx-auto px-4 md:px-8 flex justify-between items-center">

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
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-bold text-[#8C7A6B] hover:opacity-70">ログイン</a>
                        <a href="{{ route('user_register') }}"
                            class="ml-4 text-xs font-bold text-white bg-[#C1A173] px-4 py-2 rounded-full hover:bg-[#B69666] shadow-sm transition-all">はじめる</a>
                    @endguest
                </div>
            </div>
        </nav>

        {{-- --- メインコンテンツ --- --}}
        <main class="py-6">
            @yield('content')
        </main>

        @yield('js')
    </div>
</body>

</html>
