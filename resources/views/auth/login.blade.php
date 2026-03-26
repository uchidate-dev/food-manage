@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="container nt-login-bg">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card nt-login-card">

                <h2 class="nt-login-h2">ログイン</h2>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}" class="nt-login-form" novalidate>
                        @csrf

                        {{-- メールアドレス（縦並び） --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">メールアドレス</label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        {{-- パスワード（縦並び） --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">パスワード</label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password" required autocomplete="current-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        {{-- ログイン保持（縦並び） --}}
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                ログイン状態を保持する
                            </label>
                        </div>

                        {{-- ボタン（横幅いっぱい推奨） --}}
                        <button type="submit" class="btn nt-btn-orange w-100">
                            ログイン
                        </button>

                        {{-- パスワード忘れリンク（ボタンの下） --}}
                        @if (Route::has('password.request2'))
                        <div class="mt-2">
                            <a class="btn btn-link p-0" href="{{ route('password.request2') }}">
                                パスワードをお忘れですか？
                            </a>
                        </div>
                        @endif
                    </form>
                        <div class="mt-3 text-center">
                            <a href="{{ route('user_register') }}">新規登録はこちら</a>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection