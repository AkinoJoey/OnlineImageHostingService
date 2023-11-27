<main class="container">
    <h2 class="text-align-center"><a class="text-decoration-none" href="/">Pix Place</a></h2>
    <div>
        <p class="text-align-center">の削除を希望する場合は下の削除ボタンをクリックしてください。</p>
        <div class="row center-xs">
            <button id="trigger" type="submit" role="button" class="w-50">削除</button>
            <!-- modal -->
            <dialog id="modal">
                <article class="modal mt-2">
                    <h3>Confirm</h3>
                    <p>本当に画像を削除してもよろしいですか？</p>
                    <p>Yesを押すと画像が削除されます。</p>
                    <footer class="mt-0">
                        <a href="#" id="cancel" role="button" class="secondary">Cancel</a>
                        <a href="#" id="delete" role="button">Yes</a>
                    </footer>
                </article>
            </dialog>
        </div>
    </div>
</main>
<script>
    const trigger = document.getElementById('trigger');
    const modal = document.getElementById('modal');
    const cancelBtn = document.getElementById('cancel');

    trigger.addEventListener("click", function() {
        modal.style.display = 'block';
    })

    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    })
</script>