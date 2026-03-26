<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- タイトルは「なにたべ」 -->
    <title>なにたべ</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap Icons（通知画面/pop-up画面/Home画面で使用） -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>    

    <!-- Scripts -->
    @vite(['resources/sass/app.scss'])
    <!-- Common CSS -->
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <!-- Page CSS -->
    @yield('css')
</head>

<body>
    <div id="app">

        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm nt-header">
            <div class="container-fluid">
                <a class="navbar-brand nt-brand nt-brand-font"
                   href="{{ auth()->check() ? url('/home') : url('/login') }}">
                    なにたべ
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="navbar-collapse">
                    <ul class="navbar-nav me-auto"></ul>

                    <ul class="navbar-nav ms-auto align-items-center gap-2">

                        @auth

                            {{-- 挨拶：全員に表示 --}}
                            <li class="nav-item">
                                <span class="nav-link nt-brand-font">
                                    {{ $greeting }} {{ optional(auth()->user())->name }}さん！
                                </span>
                            </li>

                            {{-- role=1（管理者）だけ：ホームの時だけ 表示ユーザー切替 + ユーザー一覧 --}}
                            @if (optional(auth()->user())->role == 1 && request()->is('home'))
                                <li class="nav-item">
                                    <form method="GET" action="{{ url('/home') }}"
                                        class="d-flex align-items-center gap-2">
                                        <span class="nt-brand-font" style="white-space:nowrap;">表示ユーザー:</span>

                                        <select name="uid" class="form-select form-select-sm"
                                            onchange="this.form.submit()">
                                            @foreach ($usersForFilter ?? collect() as $u)
                                                <option value="{{ $u->id }}"
                                                    {{ ($selectedUserId ?? auth()->id()) == $u->id ? 'selected' : '' }}>
                                                    {{ $u->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </li>

                                <li class="nav-item">
                                    <a class="btn btn-sm nt-btn-orange-outline" href="{{ route('user_list') }}">
                                        ユーザー一覧
                                    </a>
                                </li>
                            @endif

                            {{-- ログアウト：全員に表示 --}}
                            <li class="nav-item">
                                <a class="btn btn-sm nt-btn-orange" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    ログアウト
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>

                        @endauth

                        @guest

                            {{-- 未ログインユーザー：ログイン --}}
                            <li class="nav-item">
                                <a class="btn btn-sm nt-btn-orange-outline" href="{{ route('login') }}">
                                    ログイン
                                </a>
                            </li>

                            {{-- 未ログインユーザー：新規登録 --}}
                            <li class="nav-item">
                                <a class="btn btn-sm nt-btn-orange" href="{{ route('user_register') }}">
                                    新規登録
                                </a>
                            </li>

                        @endguest

                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>

        {{-- ★ページ個別JS差し込み口（homeのpopup.jsがここに入る） --}}
        @yield('js')
    </div>
</body>

</html>