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
            $extension = pathinfo($imageInput['name'])['extension'];

            $hashedName = hash('sha256', uniqid(mt_rand(), true)) . '.' . $extension;
            $uploadDir =   __DIR__  .   '/../uploads/'; 
            $subdirectory = substr($hashedName, 0, 2);
            $imagePath = $uploadDir .  $subdirectory. '/' . $hashedName;

            // // アップロード先のディレクトリがない場合は作成
            // if(!is_dir(dirname($imagePath))) mkdir(dirname($imagePath), 0755, true);
            // // アップロードにした場合は失敗のメッセージを送る
            // if (!move_uploaded_file($imageInput['tmp_name'], $imagePath)) return array('success' => false, 'message' => 'Upload failed.');

            // DatabaseHelper::insertImageData($imagePath);

            return new JSONRenderer(array("tet" => $imagePath));
        }
    ]
];
