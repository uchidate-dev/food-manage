// ingredient_update.js　　[食材編集画面]

// 削除モーダルを開く
function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

// 削除モーダルを閉じる
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// トースト通知のアニメーション
window.onload = function() {
    const toast = document.getElementById('toast');
    if (toast) {
        setTimeout(() => {
            toast.classList.add('toast-enter');
        }, 100);
        setTimeout(() => {
            toast.classList.remove('toast-enter');
            toast.classList.add('toast-leave');
        }, 3000);
    }
}
