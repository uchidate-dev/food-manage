document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;

            const response = await fetch(`/recipes/${id}/favorite`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.favorite_flg === 1) {
                btn.textContent = '♥';
                btn.classList.remove('text-stone-400');
                btn.classList.add('text-pink-600');
            } else {
                btn.textContent = '♡';
                btn.classList.remove('text-pink-600');
                btn.classList.add('text-stone-400');
            }
        });
    });
});