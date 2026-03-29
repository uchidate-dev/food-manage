document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            // アイコン要素を取得
            const icon = btn.querySelector('i');
            try {
                // web.phpに合わせて /recipe/{id}/favorite に送信！
                const response = await fetch(`/recipe/${id}/favorite`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                // Controllerから返ってくる is_favorited で判断
                if (data.is_favorited) {
                    // いいねON（詳細画面用）
                    icon.classList.remove('bi-heart', 'text-gray-300');
                    icon.classList.add('bi-heart-fill', 'text-[#C1A173]');

                    // いいねON（一覧画面用）
                    btn.classList.remove('text-gray-300');
                    btn.classList.add('text-red-400');
                } else {
                    // いいねOFF（共通）
                    icon.classList.remove('bi-heart-fill', 'text-[#C1A173]');
                    icon.classList.add('bi-heart', 'text-gray-300');

                    btn.classList.remove('text-red-400');
                    btn.classList.add('text-gray-300');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });
});
