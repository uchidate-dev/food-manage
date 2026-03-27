// ゴミ箱やチェックボタンを押した時のAjax削除処理
function deleteIngredient(id, buttonElement) {
    // 1. 本当に消すか確認
    if (!confirm('この食材を使い切りましたか？（リストから削除されます）')) {
        return;
    }

    // 2. 連続で押せないようにボタンを無効化
    buttonElement.disabled = true;

    // 3. 裏側でこっそりControllerにお願い（Ajax）
    fetch(`/ingredient_delete/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 4. 成功したら、その食材の行だけをフワッと消す！
            const itemRow = buttonElement.closest('.flex.justify-between');

            itemRow.style.transition = "opacity 0.4s ease, transform 0.4s ease";
            itemRow.style.opacity = "0";
            itemRow.style.transform = "translateX(20px)";

            setTimeout(() => {
                itemRow.remove(); // 完全に消し去る
            }, 400);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('削除に失敗しちゃいました😭');
        buttonElement.disabled = false;
    });
}
