{{--  共通パーツ：削除確認モーダル --}}
<div id="sharedDeleteModal"
    class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-[#4A4A4A]/40 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl p-8 sm:p-10 max-w-sm w-full mx-4 text-center transform scale-95 transition-transform duration-300"
        id="sharedDeleteModalContent">

        <div class="text-red-400 text-5xl mb-5"><i class="bi bi-exclamation-circle"></i></div>

        {{-- 文字サイズを大きく --}}
        <h3 class="text-xl font-bold text-gray-800 tracking-widest mb-4">本当に削除しますか？</h3>

        <p class="text-sm font-medium text-gray-500 leading-relaxed mb-8">
            {{-- 食材名も大きく --}}
            <span id="deleteItemName" class="font-bold text-red-500 block mb-2 text-lg"></span>
            削除したデータは元に戻すことができません。<br>本当によろしいですか？
        </p>

        <form id="sharedDeleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex gap-4">
                <button type="button" onclick="closeSharedDeleteModal()"
                    class="flex-1 border border-gray-200 text-gray-500 px-4 py-3.5 rounded-xl text-sm font-bold transition-colors hover:bg-gray-50 tracking-widest">
                    キャンセル
                </button>
                <button type="submit"
                    class="flex-1 bg-red-400 text-white px-4 py-3.5 rounded-xl text-sm font-bold shadow-md transition-colors hover:bg-red-500 tracking-widest">
                    削除する
                </button>
            </div>
        </form>
    </div>
</div>

