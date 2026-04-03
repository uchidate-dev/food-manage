<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Kitchen Stock | 毎日の献立に、ほんの少しの余白を。</title>

    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text x=%2250%%22 y=%2250%%22 style=%22dominant-baseline:central;text-anchor:middle;font-size:90px;%22>🍳</text></svg>">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Quicksand:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
        }

        .font-logo {
            font-family: 'Quicksand', sans-serif;
        }
    </style>
</head>

<body class="bg-[#FAF9F6] text-[#4A4A4A] min-h-screen flex flex-col selection:bg-[#C1A173] selection:text-white">

    {{-- スマホ用ヘッダー（ロゴ） --}}
    <div class="lg:hidden w-full px-8 py-6 bg-[#FAF9F6] flex justify-center z-20 border-b border-[#EAE4DD]/50">
        <a href="/"
            class="text-xl font-logo font-semibold tracking-tight text-[#8C7A6B] hover:opacity-70 transition-opacity">
            My Kitchen <span class="text-[#C1A173]">Stock</span>
        </a>
    </div>

    {{-- メインコンテンツ（PC時は左右分割、スマホ時は上下配置） --}}
    <div class="flex flex-col lg:flex-row flex-1">

        {{-- 左エリア --}}
        <div
            class="w-full lg:w-5/12 flex flex-col px-8 md:px-12 lg:px-16 py-10 z-10 bg-[#FAF9F6] min-h-[50vh] lg:h-screen order-2 lg:order-1">

            {{--  PC用ヘッダー（ロゴ）：スマホの時は隠す --}}
            <nav class="hidden lg:flex justify-between items-center w-full mb-auto">
                <a href="/"
                    class="text-xl font-logo font-semibold tracking-tight text-[#8C7A6B] hover:opacity-70 transition-opacity">
                    My Kitchen <span class="text-[#C1A173]">Stock</span>
                </a>
            </nav>

            {{-- メインコピー --}}
            <div class="my-auto lg:pt-8 mt-4 lg:mt-0">

                <p class="text-[#C1A173] text-[10px] font-bold tracking-[0.3em] uppercase mb-8">
                    <span class="inline-block border-b border-[#C1A173] pb-1">
                        AI Recipe & Stock
                    </span>
                </p>

                <h1
                    class="text-3xl lg:text-4xl xl:text-5xl font-light text-[#4A3F35] leading-[1.6] tracking-widest mb-8">
                    毎日の献立に、<br>
                    ほんの少しの<span class="font-medium text-[#8C7A6B]">余白</span>を。
                </h1>

                <p class="text-xs text-gray-500 leading-[2.2] tracking-widest mb-12">
                    冷蔵庫の前でフリーズする朝は、もう終わりにしよう。<br>
                    今ある食材を選ぶだけで、AIが最適な献立をサクッと提案。<br>
                    忙しいあなたの「考える時間」をゼロにする、AIレシピ＆食材管理アプリ。
                </p>

                {{-- ボタン --}}
                <div class="flex flex-col xl:flex-row gap-4 w-full">
                    <a href="{{ route('user_register') }}"
                        class="text-center bg-[#8C7A6B] text-white px-6 py-4 text-xs font-bold tracking-[0.2em] hover:bg-[#7A6A5C] transition-colors">
                        無料で使ってみる
                    </a>

                    <a href="{{ route('login') }}"
                        class="text-center bg-transparent border border-[#EAE4DD] text-[#8C7A6B] px-6 py-4 text-xs font-bold tracking-[0.2em] hover:bg-white transition-colors">
                        デモ画面を見る
                    </a>
                </div>

            </div>

            <div class="mt-auto hidden lg:block"></div>
        </div>

        {{-- 右エリア（画像） スマホ時はヘッダーのすぐ下（順序1番目）に来る --}}
        <div class="w-full lg:w-7/12 h-[35vh] lg:h-screen relative overflow-hidden order-1 lg:order-2">
            <img src="{{ asset('images/hero-bg.jpg') }}" alt="子どものためのやさしいお弁当" class="w-full h-full object-cover">
        </div>

    </div>

</body>

</html>
