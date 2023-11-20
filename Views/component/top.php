<main class="container">
    <h2 class="text-align-center"><a class="text-decoration-none" href="/">Pix Place</a></h2>

    <div class="center-xs mt-2">
        <form action="/" method="post" enctype="multipart/form-data">
            <label for="imageInput">画像を選択:</label>
            <div class="text-align-center">
                <input type="file" id="imageInput" name="imageInput" accept="image/png, image/jpeg, image/gif" required>
            </div>
            <button id="create-btn" role="button" class="w-50 mt-2">POST</button>
        </form>
    </div>
</main>