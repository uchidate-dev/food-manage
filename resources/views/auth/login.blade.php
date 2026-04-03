@extends('layouts.app')

@section('content')
    <div class="bg-[#FAF9F6] min-h-[85vh] flex flex-col items-center justify-center px-4 sm:px-6 relative">

        {{-- 画面左上に「LPに戻る」矢印ボタン --}}
        <div class="absolute top-4 left-4 sm:top-8 sm:left-8">
            <a href="/"
                class="text-[11px] font-bold text-gray-400 hover:text-[#C1A173] tracking-widest transition-colors flex items-center">
                <i class="bi bi-arrow-left mr-2"></i> LPに戻る
            </a>
        </div>

        <div class="w-full max-w-md bg-white rounded-3xl shadow-sm border border-[#EAE4DD] px-8 py-12 sm:px-12">

            {{--  アプリロゴ ＆ メッセージ --}}
            <div class="text-center mb-10">
                {{-- 中央の大きなロゴもクリックできるように <a> タグで囲みました！ --}}
                <a href="/" class="inline-block hover:opacity-70 transition-opacity">
                    <h1 class="text-3xl md:text-4xl font-logo font-bold text-[#8C7A6B] tracking-tight mb-2">
                        My Kitchen <span class="text-[#C1A173]">Stock</span>
                    </h1>
                </a>
                <p class="text-[11px] font-bold tracking-[0.2em] text-[#C1A173] uppercase">
                    Welcome back
                </p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate>
                @csrf

                {{-- メールアドレス --}}
                <div>
                    <label for="email" class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2">
                        <i class="bi bi-envelope mr-1 text-[#C1A173]"></i> メールアドレス
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all @error('email') border-red-300 bg-red-50 @enderror">

                    @error('email')
                        <p class="text-red-400 text-[10px] mt-2 font-bold tracking-widest">{{ $message }}</p>
                    @enderror
                </div>

                {{-- パスワード --}}
                <div>
                    <label for="password" class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2">
                        <i class="bi bi-lock mr-1 text-[#C1A173]"></i> パスワード
                    </label>
                    <input id="password" type="password" name="password" required
                        class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all @error('password') border-red-300 bg-red-50 @enderror">

                    @error('password')
                        <p class="text-red-400 text-[10px] mt-2 font-bold tracking-widest">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ログイン保持 --}}
                <div class="flex items-center pt-2">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                        class="w-4 h-4 text-[#C1A173] bg-[#FAF9F6] border-[#EAE4DD] rounded focus:ring-[#C1A173] focus:ring-2 accent-[#C1A173] cursor-pointer">
                    <label for="remember"
                        class="ml-2 text-[11px] font-bold text-gray-400 tracking-widest cursor-pointer hover:text-gray-500 transition-colors">
                        ログイン状態を保持する
                    </label>
                </div>

                {{-- ログインボタン --}}
                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-[#8C7A6B] py-4 rounded-xl text-xs font-bold text-white shadow-md shadow-[#8C7A6B]/20 hover:bg-[#7A695A] hover:-translate-y-0.5 transition-all tracking-[0.2em]">
                        ログインする
                    </button>
                </div>
            </form>

            {{-- デモユーザー専用フォーム --}}
            <div class="mt-8">
                <div class="relative flex items-center justify-center mb-6">
                    <div class="flex-grow border-t border-[#EAE4DD]"></div>
                    <span class="flex-shrink-0 px-4 text-[10px] font-bold text-[#8C7A6B] tracking-widest">
                        デモユーザーログイン
                    </span>
                    <div class="flex-grow border-t border-[#EAE4DD]"></div>
                </div>

                <form method="POST" action="{{ route('login') }}" class="m-0">
                    @csrf
                    <input type="hidden" name="email" value="guest@example.com">
                    <input type="hidden" name="password" value="password123">

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-[#C1A173] to-[#A88C61] py-4 rounded-xl text-xs font-bold text-white shadow-md shadow-[#C1A173]/30 hover:opacity-90 hover:-translate-y-0.5 transition-all tracking-[0.2em] flex items-center justify-center">
                        <i class="bi bi-door-open mr-2 text-lg"></i> ゲストとして1クリックでログイン
                    </button>
                </form>
            </div>

            {{-- 新規登録への導線 --}}
            <div class="mt-8 text-center border-t border-[#EAE4DD] pt-6">
                <p class="text-[10px] font-bold text-gray-400 tracking-widest mb-3">アカウントをお持ちでない方</p>
                <a href="{{ route('user_register') }}"
                    class="inline-block border border-[#C1A173] text-[#C1A173] px-8 py-3 rounded-full text-[11px] font-bold hover:bg-[#FAF9F6] transition-all tracking-widest">
                    新しく始める
                </a>
            </div>

        </div>
    </div>
@endsection
