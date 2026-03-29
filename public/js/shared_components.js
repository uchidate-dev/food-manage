
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
