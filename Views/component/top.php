<main class="container">
    <h2 class="text-align-center"><a class="text-decoration-none" href="/">Pix Pocket</a></h2>

    <div class="center-xs mt-2">
        <form id="myForm" enctype="multipart/form-data">
            <label for="imageInput">画像を選択:</label>
            <div class="text-align-center">
                <input type="file" id="imageInput" name="imageInput" accept="image/png, image/jpeg, image/gif" required>
            </div>
            <button id="submitter" type="submit" role="button" class="w-50 mt-2">POST</button>
        </form>
    </div>
    <dialog id="progress-window">
        <article class="modal">
            <h4>please wait...</h4>
            <progress></progress>
        </article>
    </dialog>
    <div id="modal-container"></div>

</main>
<script>
    const modal = document.getElementById('modal');
    const progressWindow = document.getElementById('progress-window');

    document.getElementById('myForm').addEventListener('submit', function(event) {
        event.preventDefault();

        let fileInput = document.querySelector('#imageInput');
        const formData = new FormData();
        formData.append('imageInput', fileInput.files[0]);
        progressWindow.open = true;

        fetch('/', {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    progressWindow.open = false;

                    const shared_url = data.shared_url;
                    const delete_url = data.delete_url;

                    const container = document.getElementById('modal-container');
                    container.innerHTML = `
                    <dialog id="modal" class="scale-in-ver-center" open>
                        <article class="modal">
                            <img class="puff-in-center" src="/images/checkmark.svg" ></img>
                            <h3 class="mt-2">Upload Complete!</h3>     
                            <p>共有用URL:<br><a href="${shared_url}" target="_blank" rel="noopener">${shared_url}</a></p>
                            <p>削除用URL:<br><a href="${delete_url}" target="_blank" rel="noopener">${delete_url}</a></p>
                        </article>
                    </dialog>
                    `;
                } else {
                    progressWindow.open = false;
                    alert(data.message);
                }
            })
            .catch(error => {
                progressWindow.open = false;
                console.error('Error:', error);
            });
    });
</script>