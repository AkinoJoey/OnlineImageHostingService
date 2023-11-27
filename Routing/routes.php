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
            $mime = $finfo->file($imageInput['tmp_name']);
            $extension = explode('/', $mime)[1];

            $filename = hash('sha256', uniqid(mt_rand(), true)) . '.' . $extension;
            $uploadDir =   '../uploads/'; 
            $subdirectory = substr($filename, 0, 2);
            $imagePath = $uploadDir .  $subdirectory. '/' . $filename;

            // アップロード先のディレクトリがない場合は作成
            if(!is_dir(dirname($imagePath))) mkdir(dirname($imagePath), 0755, true);
            // アップロードにした場合は失敗のメッセージを送る
            if (!move_uploaded_file($imageInput['tmp_name'], $imagePath)) return array('success' => false, 'message' => 'Upload failed.');

            $shared_url = hash('sha256', uniqid(mt_rand(), true));
            $delete_url = hash('sha256', uniqid(mt_rand(), true));
            $insertResult = DatabaseHelper::insertImageData($imagePath, $shared_url, $delete_url, $mime);

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
            $shared_url = $paths[2];

            $data = DatabaseHelper::getImageData($shared_url);

            if (!$data) {
                http_response_code(404);
                return new HTMLRenderer('component/404', ['errormsg' => "Page not found"]);
            }
            $path = $data['path'];
            $viewCount = $data['view_count'];
            $mime = $data['mime'];

            $image = base64_encode(file_get_contents($path));

            return new HTMLRenderer('component/sharedImage', ['image'=> $image, 'mime' => $mime ,'viewCount' => $viewCount]);
        }
    ],
    'delete' => [
        'GET' => function() : HTMLRenderer {
            $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $paths = explode('/', $url);

            // hash部分がない場合は404を出す
            if (count($paths) < 3) {
                http_response_code(404);
                return new HTMLRenderer('component/404', ['errormsg' => 'Page not found']);
            }
            // TO:DO バリデーション
            $delete_url = $paths[2];

            return new HTMLRenderer('component/deleteImage');
        }
    ]
];
