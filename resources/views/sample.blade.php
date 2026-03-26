@extends('layouts.app')

@section('css')
<!-- app.blade.phpの@yield('css')に挿入される -->
<link rel="stylesheet" href="{{ asset('css/sample.css') }}">
@endsection

@section('content')
<!-- app.blade.phpの@yield('content')に挿入される -->
<div class="container">
    <h1>サンプル画面です。</h1>
</div>
@endsection

@section('js')
<!-- app.blade.phpの@yield('js')に挿入される -->
<script src="{{ asset('js/sample.js') }}"></script>
@endsection