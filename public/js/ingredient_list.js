// ingredient_list.js　[食材一覧画面]

window.onload = function() {
    const toast = document.getElementById('toast');
    // トーストが画面に存在している時だけ、アニメーションを動かす
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

// 削除モーダルを開く（IDを受け取ってFormの送信先をセット）
function openDeleteModal(id) {
    const form = document.getElementById('deleteForm');
    form.action = '/ingredient_delete/' + id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

// 削除モーダルを閉じる
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
