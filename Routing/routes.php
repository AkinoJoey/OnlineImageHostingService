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

            $hash_for_shared_url = hash('sha256', uniqid(mt_rand(), true));
            $hash_for_delete_url = hash('sha256', uniqid(mt_rand(), true));
            $shared_url = '/' . $extension . '/' . $hash_for_shared_url;
            $delete_url = '/' .  'delete' . '/' . $hash_for_delete_url;
            $insertResult = DatabaseHelper::insertImageData($imagePath, $shared_url, $delete_url, $mime);

            if ($insertResult) {
                return new JSONRenderer(["success" => true, "shared_url" => $shared_url, "delete_url"=> $delete_url]);
            } else {
                return new JSONRenderer(["success" => false, "message" => "Database operation failed"]);
            }
        }
    ],
    'getImage' => [
        'GET' => function(): HTMLRenderer{
            $shared_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
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
            $delete_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $shared_url = DatabaseHelper::getSharedUrl($delete_url);

            if (!$shared_url) {
                http_response_code(404);
                return new HTMLRenderer('component/404', ['errormsg' => "Page not found"]);
            }

            return new HTMLRenderer('component/deleteImage', ['shared_url' => $shared_url]);
        },
        'POST' => function () : JSONRenderer {
            $json = file_get_contents("php://input");
            $shared_url = json_decode($json, true)['shared_url'];
            $result = DatabaseHelper::deleteImageData($shared_url);

            if ($result) {
                return new JSONRenderer(["success" => true]);
            } else {
                return new JSONRenderer(["success" => false, "message" => "Database operation failed"]);
            }

            return new JSONRenderer(["success" => true, "test" => $shared_url]);
        }
        
    ]
];
