
    //  モーダルを開く魔法
    function openSharedDeleteModal(actionUrl, itemName = '') {
        document.getElementById('sharedDeleteForm').action = actionUrl;

        const nameSpan = document.getElementById('deleteItemName');
        if (itemName) {
            nameSpan.textContent = `「${itemName}」`;
            nameSpan.style.display = 'block';
        } else {
            nameSpan.style.display = 'none';
        }

        const modal = document.getElementById('sharedDeleteModal');
        const content = document.getElementById('sharedDeleteModalContent');

        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    //  モーダルを閉じる
    function closeSharedDeleteModal() {
        const modal = document.getElementById('sharedDeleteModal');
        const content = document.getElementById('sharedDeleteModalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

// ==========================================
// スマホ用ハンバーガーメニューの開閉制御
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');

    if (btn && menu) {
        btn.addEventListener('click', function() {
            // メニューの表示・非表示を切り替え
            menu.classList.toggle('hidden');

            // アイコンをメニューと閉じるで切り替え
            const icon = btn.querySelector('i');
            if (menu.classList.contains('hidden')) {
                icon.classList.remove('bi-x-lg');
                icon.classList.add('bi-list');
            } else {
                icon.classList.remove('bi-list');
                icon.classList.add('bi-x-lg');
            }
        });
    }
});
