<?php

use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;

return [
    '' => [
        'GET' => function (): HTMLRenderer {
            return new HTMLRenderer('component/top');
        },
        'POST' => function() : JSONRenderer {
            $tmpPath = $_FILES['imageInput']['tmp_name'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmpPath);
            $byteSize = filesize($tmpPath);

            if(!ValidationHelper::validateFileType($mime)) return new JSONRenderer(['success' => false, 'message' => 'png,jpg,gif以外の拡張子には対応していません。']);
            if(!ValidationHelper::isFileSizeSmallerThan5MB($byteSize)) return new JSONRenderer(['success' => false, 'message' => '5MBより大きい画像はアップロードできません。']);

            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $totalUploadSizeToday = DatabaseHelper::getTotalUploadSizeToday($ipAddress);
            if (!ValidationHelper::checkUploadSizeLimit($totalUploadSizeToday, $byteSize)) return new JSONRenderer(['success' => false, 'message' => '1日のアップロード合計容量は5MBです。']);

            $extension = explode('/', $mime)[1];

            $filename = hash('sha256', uniqid(mt_rand(), true)) . '.' . $extension;
            $uploadDir =   '../uploads/'; 
            $subdirectory = substr($filename, 0, 2);
            $imagePath = $uploadDir .  $subdirectory. '/' . $filename;

            // アップロード先のディレクトリがない場合は作成
            if(!is_dir(dirname($imagePath))) mkdir(dirname($imagePath), 0755, true);
            // アップロードにした場合は失敗のメッセージを送る
            if (!move_uploaded_file($tmpPath, $imagePath)) return new JSONRenderer(['success' => false, 'message' => 'アップロードに失敗しました。']);

            $hash_for_shared_url = hash('sha256', uniqid(mt_rand(), true));
            $hash_for_delete_url = hash('sha256', uniqid(mt_rand(), true));
            $shared_url = '/' . $extension . '/' . $hash_for_shared_url;
            $delete_url = '/' .  'delete' . '/' . $hash_for_delete_url;
            $insertResult = DatabaseHelper::insertImageData($imagePath, $shared_url, $delete_url, $mime);

            if ($insertResult) {
                return new JSONRenderer(["success" => true, "shared_url" => $shared_url, "delete_url"=> $delete_url]);
            } else {
                return new JSONRenderer(["success" => false, "message" => "データベースの操作に失敗しました。"]);
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

            if(!DatabaseHelper::updateImageData($shared_url)) return new JSONRenderer(['success' => false, 'message' => 'データベースの操作に失敗しました。']);

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
            $imagePath = DatabaseHelper::getImageData($shared_url)['path'];
            $deleteFromDBresult = DatabaseHelper::deleteImageData($shared_url);

            if (!$deleteFromDBresult) return new JSONRenderer(["success" => false, "message" => "データベースの操作に失敗しました"]);

            $deleteFromStorageResult = unlink($imagePath);
            if (!$deleteFromStorageResult)return new JSONRenderer(["success" => false, "message" => "画像の削除に失敗しました。"]);

            return new JSONRenderer(["success" => true]);
        }
        
    ]
];
