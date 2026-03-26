@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user_edit.css') }}">
@endsection

@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <a href="{{ route('user_list') }}">← 一覧に戻る</a>

            <div class="card mt-3">
                <div class="card-header text-center">
                    <h5>ユーザー情報編集</h5>
                </div>

                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user_update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label>氏名</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>メールアドレス</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>新しいパスワード（任意）</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>パスワード確認</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                            >
                        </div>

                        @if($user->id != 1)
                            <div class="mb-3">
                                <label>権限</label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror">
                                    <option value="0" {{ old('role', $user->role) == 0 ? 'selected' : '' }}>一般ユーザー</option>
                                    <option value="1" {{ old('role', $user->role) == 1 ? 'selected' : '' }}>管理者</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-3 form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="mail_flg"
                                value="1"
                                {{ old('mail_flg', $user->mail_flg) == 1 ? 'checked' : '' }}
                            >
                            <label class="form-check-label">
                                お知らせメールを受け取る
                            </label>
                        </div>

                        <button type="submit" class="btn btn-main w-100">
                            変更を保存
                        </button>

                    </form>

                    @if($user->id != 1)
                        <form method="POST" action="{{ route('user_delete', $user->id) }}"
                            onsubmit="return confirm('削除しますか？')" class="mt-3">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger w-100">
                                このユーザーを削除
                            </button>

                        </form>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

@endsection