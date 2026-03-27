@extends('layouts.app')

@section('content')
    <div class="bg-[#FAF9F6] min-h-[85vh] flex flex-col items-center justify-center py-12 px-4 sm:px-6">

        <div class="w-full max-w-md bg-white rounded-3xl shadow-sm border border-[#EAE4DD] px-8 py-10 sm:px-12">

            {{--  タイトル　--}}
            <div class="text-center mb-10">
                <h1 class="text-3xl md:text-4xl font-logo font-bold text-[#8C7A6B] tracking-tight mb-2">
                    My Kitchen <span class="text-[#C1A173]">Stock</span>
                </h1>
                <p class="text-[11px] font-bold tracking-[0.2em] text-[#C1A173] uppercase">
                    Create Account
                </p>
            </div>

            {{-- エラーメッセージ　--}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-100 rounded-xl p-4 mb-8">
                    <ul class="text-[11px] font-bold text-red-500 tracking-widest space-y-1 mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="bi bi-exclamation-circle mr-1"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('user_store') }}" class="space-y-6">
                @csrf

                {{-- 氏名 --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2">
                        <i class="bi bi-person mr-1 text-[#C1A173]"></i> お名前
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="例：山田　花子"
                        class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all">
                </div>

                {{-- メールアドレス --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2">
                        <i class="bi bi-envelope mr-1 text-[#C1A173]"></i> メールアドレス
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="mail@example.com"
                        class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all">
                </div>

                {{-- パスワード --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2">
                        <i class="bi bi-lock mr-1 text-[#C1A173]"></i> パスワード <span
                            class="text-gray-300 font-normal ml-1">※8文字以上</span>
                    </label>
                    <input type="password" name="password" required
                        class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all">
                </div>

                {{-- パスワード確認 --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 tracking-widest mb-2">
                        <i class="bi bi-check2-circle mr-1 text-[#C1A173]"></i> パスワード（再確認）
                    </label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-[#FAF9F6] border border-[#EAE4DD] rounded-xl px-4 py-3.5 text-sm font-bold text-gray-700 focus:outline-none focus:border-[#C1A173] focus:ring-2 focus:ring-[#C1A173]/20 transition-all">
                </div>


                {{-- ボタンエリア --}}
                <div class="pt-6 space-y-4">
                    <button type="submit"
                        class="w-full bg-[#8C7A6B] py-4 rounded-xl text-xs font-bold text-white shadow-md shadow-[#8C7A6B]/20 hover:bg-[#7A695A] hover:-translate-y-0.5 transition-all tracking-[0.2em]">
                        登録してはじめる
                    </button>

                    <div class="text-center pt-2">
                        <a href="{{ route('login') }}"
                            class="text-[10px] font-bold text-gray-400 hover:text-[#C1A173] tracking-widest transition-colors border-b border-transparent hover:border-[#C1A173] pb-1">
                            すでにアカウントをお持ちの方
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection
