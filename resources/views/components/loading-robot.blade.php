{{-- 🍳 共通パーツ：AIローディング画面 --}}
<div id="loading-screen"
    class="fixed inset-0 bg-[#FAF9F6]/80 z-[100] hidden flex-col items-center justify-center backdrop-blur-sm transition-opacity">
    <div class="animate-bounce mb-6">
        <i class="bi bi-robot text-6xl text-[#C1A173]"></i>
    </div>
    <h3 class="text-2xl font-bold text-[#4A3F35] tracking-widest mb-3 font-sans">
        AIシェフが調理中...
    </h3>
    <p class="text-sm font-bold text-[#8C7A6B] tracking-widest animate-pulse mt-2">
        最高のレシピを考えています🍳✨
    </p>
</div>

<script>
    function showLoading(event, url = null) {
        //  もし <a>タグからのクリックなら、一旦「次のページへ飛ぶ」のをストップ
        if (event) {
            event.preventDefault();
        }

        const loader = document.getElementById('loading-screen');
        if (loader) {
            loader.classList.remove('hidden');
            loader.classList.add('flex');
        }

        // ロボットが画面に出るための時間（0.1秒）を作ってから、手動でページ移動
        if (url) {
            setTimeout(() => {
                window.location.href = url;
            }, 100);
        }
    }
</script>
