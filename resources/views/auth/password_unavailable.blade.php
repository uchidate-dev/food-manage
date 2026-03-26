@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/notice.css') }}">
@endsection

@section('content')
<div class="container nt-notice-bg">
  <div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
      <div class="card nt-notice-card">
        <div class="card-body">
          <div class="row align-items-center g-4">
            <div class="col-9">
              <h2 class="nt-notice-title">パスワード再設定について</h2>
              <p class="nt-notice-text mb-2">メールによる再設定は未対応です。</p>
              <p class="nt-notice-text mb-3 nt-notice-one-line">管理者にパスワード変更を依頼してください。</p>

              <a href="{{ route('login') }}" class="nt-notice-link">ログイン画面へ戻る</a>
            </div>

            <div class="col-3 text-center nt-notice-right" aria-hidden="true">
              <i class="bi bi-cone-striped nt-notice-icon"></i>
            </div>
          </div>
        </div>
      </div><!-- /.card -->
    </div>
  </div>
</div>
@endsection