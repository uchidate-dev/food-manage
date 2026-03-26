@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user_register.css') }}">
@endsection

@section('content')

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow-sm border-0">
                    <div class="card-header text-center">
                        <h2 class="h5 mb-0 py-1 fw-bold">新規ユーザー登録</h2>
                    </div>

                    <div class="card-body p-4">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('user_store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-bold">氏名</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">メールアドレス</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">パスワード</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">パスワード再確認</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <div class="mb-4 form-check">
                                <input class="form-check-input"
                                    type="checkbox"
                                    name="mail_flg"
                                    value="1">
                                <label class="form-check-label">
                                    お知らせメールを受け取る
                                </label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-main btn-lg">
                                    登録する
                                </button>

                                <a href="{{ route('login') }}"
                                class="btn btn-outline-secondary">
                                    キャンセル（ログインへ）
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection