<?php

use Helpers\DatabaseHelper;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;

return [
    '' => [
        'GET' => function (): HTMLRenderer {
            return new HTMLRenderer('component/top');
        },
        'POST' => function() : JSONRenderer {
            $imageInput = $_FILES['imageInput'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->file($imageInput['tmp_name']);
            $extension = explode('/', $mime_type)[1];

            $hashedName = hash('sha256', uniqid(mt_rand(), true)) . '.' . $extension;
            $uploadDir =   __DIR__  .   '/../uploads/'; 
            $subdirectory = substr($hashedName, 0, 2);
            $imagePath = $uploadDir .  $subdirectory. '/' . $hashedName;

            // アップロード先のディレクトリがない場合は作成
            if(!is_dir(dirname($imagePath))) mkdir(dirname($imagePath), 0755, true);
            // アップロードにした場合は失敗のメッセージを送る
            if (!move_uploaded_file($imageInput['tmp_name'], $imagePath)) return array('success' => false, 'message' => 'Upload failed.');

            $shared_url = hash('sha256', uniqid(mt_rand(), true));
            $delete_url = hash('sha256', uniqid(mt_rand(), true));
            $insertResult = DatabaseHelper::insertImageData($imagePath, $shared_url, $delete_url);

            if ($insertResult) {
                return new JSONRenderer(["success" => true, "url" => "{$extension}/{$shared_url}"]);
            } else {
                return new JSONRenderer(["success" => false, "message" => "Database operation failed"]);
            }
        }
    ],
    'getImage' => [
        'GET' => function(): HTMLRenderer{
            $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $paths = explode('/', $url);

            // hash部分がない場合は404を出す
            if (count($paths) < 3) {
                http_response_code(404);
                return new HTMLRenderer('component/404', ['errormsg' => 'Page not found']);
            }
            // TO:DO バリデーション
            $extension = $paths[1];
            $shared_url = $paths[2];

            $data = DatabaseHelper::getImageData($shared_url);

            if (!$data) {
                http_response_code(404);
                return new HTMLRenderer('component/404', ['errormsg' => "Page not found"]);
            }
            $path = $data['path'];
            $viewCount = $data['view_count'];

            $image = file_get_contents($path);
            header("Content-type: image/{$extension}");
            echo readfile($path);

            return new HTMLRenderer('component/sharedImage', ['image'=> $image, 'viewCount' => $viewCount]);
        }
    ]
];
