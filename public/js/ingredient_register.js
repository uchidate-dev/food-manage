// ingredient_register.js　[食材登録画面]

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
