// 画面が完全に読み込まれたら実行する！
document.addEventListener('DOMContentLoaded', function () {
    // 3秒後にフワッと消える魔法
    setTimeout(() => {
        const flash = document.getElementById('flash-message');
        if (flash) {
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500); // 透明になったらHTMLから完全に消す
        }
    }, 3000);
});
