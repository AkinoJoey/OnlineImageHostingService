<main class="container">
    <h2 class="text-align-center"><a class="text-decoration-none" href="/">Pix Place</a></h2>

    <div class="center-xs mt-2">
        <form id="myForm" enctype="multipart/form-data">
            <label for="imageInput">画像を選択:</label>
            <div class="text-align-center">
                <input type="file" id="imageInput" name="imageInput" accept="image/png, image/jpeg, image/gif" required>
            </div>
            <button id="submitter" type="submit" role="button" class="w-50 mt-2">POST</button>
        </form>

    </div>
</main>
<script>

    document.getElementById('myForm').addEventListener('submit', function(event) {
        event.preventDefault();

        let fileInput = document.querySelector('#imageInput');
        const formData = new FormData();
        formData.append('imageInput', fileInput.files[0]);

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
                console.log('Success:', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>