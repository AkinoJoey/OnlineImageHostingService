<main class="container">
    <h2 class="text-align-center"><a class="text-decoration-none" href="/">Pix Place</a></h2>
    <div>
        <p class="text-align-center"><a href="<?= $shared_url ?>"><?= $shared_url ?></a>の削除を希望する場合は下の削除ボタンをクリックしてください。</p>
        <div class="row center-xs">
            <button id="trigger" type="submit" role="button" class="w-50">削除</button>
        </div>
    </div>
    <!-- Confirm modal -->
    <dialog id="confirm-modal">
        <article class="modal wobble-hor-bottom">
            <h3>Confirm</h3>
            <p>本当に画像を削除してもよろしいですか？</p>
            <p>Yesを押すと画像が削除されます。</p>
            <footer class="mt-0">
                <a href="#" id="cancel" role="button" class="secondary">Cancel</a>
                <a href="#" id="delete" role="button">Yes</a>
            </footer>
        </article>
    </dialog>

    <!-- Successful modal -->
    <dialog id="successful-modal">
        <article class="modal">
            <h3>Deletion Completed</h3>
            <p>削除が完了しました。</p>
            <footer class="mt-0">
                <a href="/" id="home" role="button">Confirmed</a>
            </footer>
        </article>
    </dialog>
</main>
<script>
    const trigger = document.getElementById('trigger');
    const confirmModal = document.getElementById('confirm-modal');
    const cancelBtn = document.getElementById('cancel');
    const deleteBtn = document.getElementById('delete');
    const successfulModal = document.getElementById('successful-modal');

    trigger.addEventListener("click", function() {
        confirmModal.open = true;
    })

    cancelBtn.addEventListener('click', function() {
        confirmModal.open = false;
    })

    deleteBtn.addEventListener('click', function() {
        fetch('/delete', {
                method: 'POST',
                body: JSON.stringify({
                    'shared_url': '<?= $shared_url ?>'
                }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    confirmModal.open = false;
                    successfulModal.open = true;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    })
</script>