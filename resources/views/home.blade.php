@extends('layouts.app')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: sans-serif; color: #333;">

        <h1
            style="font-size: 1.5rem; color: #555; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 30px;">
            Minimal Kitchen
        </h1>

        {{-- 🔴 期限切れ（優先度MAX） --}}
        @if (count($expiredItems) > 0)
            <div
                style="background-color: #fef2f2; border-left: 5px solid #ef4444; padding: 20px; margin-bottom: 30px; border-radius: 4px;">
                <h2 style="color: #b91c1c; font-size: 1.2rem; margin-top: 0;">⚠️ 期限切れ食材（早く使おう！）</h2>
                <ul style="color: #991b1b; padding-left: 20px;">
                    @foreach ($expiredItems as $item)
                        <li>{{ $item->name }} （{{ \Carbon\Carbon::parse($item->expiration_date)->format('m/d') }} 期限）</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🟡 今日・明日期限（メインの救済対象） --}}
        <div
            style="background-color: #fffbeb; border-left: 5px solid #f59e0b; padding: 20px; margin-bottom: 30px; border-radius: 4px;">
            <h2 style="color: #b45309; font-size: 1.2rem; margin-top: 0;"> 今日・明日で使い切りたい食材</h2>

            @if (count($soonItems) > 0)
                <ul style="color: #92400e; padding-left: 20px;">
                    @foreach ($soonItems as $item)
                        <li>{{ $item->name }} （{{ \Carbon\Carbon::parse($item->expiration_date)->format('m/d') }} 期限）</li>
                    @endforeach
                </ul>
                <div style="margin-top: 20px;">
                    <button
                        style="background-color: #f59e0b; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        💡 この食材でAIに献立を相談する（※実装予定）
                    </button>
                </div>
            @else
                <p style="color: #92400e; margin: 0;">現在、急いで使う食材はありません！✨</p>
            @endif
        </div>

        {{-- 🔗 リンク集 --}}
        <div style="margin-top: 40px;">
            <a href="{{ url('/ingredient_list') }}"
                style="display: inline-block; background-color: #4b5563; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px;">
                冷蔵庫の中身を見る
            </a>
        </div>

    </div>
@endsection
