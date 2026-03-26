@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user_list.css') }}">
@endsection

@section('content')

    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold" style="color: #5d4037;">ユーザー一覧</h1>

            <a href="{{ route('home') }}" class="btn btn-outline-secondary shadow-sm">
                ホームに戻る
            </a>
        </div>


        {{-- 成功メッセージ --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- エラーメッセージ --}}
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif


        <div class="card shadow-sm border-0">

            <div class="card-body p-0">

                <table class="table table-hover mb-0">

                    <thead class="custom-thead">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>氏名</th>
                            <th>メールアドレス</th>
                            <th>権限</th>
                            <th class="text-center">操作</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($users as $user)

                        <tr>

                            <td class="ps-4">{{ $user->id }}</td>

                            <td>{{ $user->name }}</td>

                            <td>{{ $user->email }}</td>

                            <td>
                                @if($user->role == 1)
                                    <span class="badge rounded-pill bg-danger">
                                        管理者
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-secondary">
                                        一般
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ url('/user_edit/'.$user->id) }}"
                                class="btn btn-sm btn-outline-secondary">
                                    編集
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center py-4">
                                ユーザーが登録されていません
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection