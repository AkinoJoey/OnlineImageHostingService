<?php
# cronでは作業ディレクトリがホームディレクトリになるから、作業ディレクトリをcronディレクトリに変更
chdir(__DIR__);


require('../autoload.php');

use Helpers\DatabaseHelper;

$oldData = DatabaseHelper::getInactiveImageData30Days();

$uploadDir = '../uploads/';

if(!empty($oldData)){
    DatabaseHelper::deleteInactiveImageData30Days();
    for($i = 0; $i < count($oldData); $i++){
        $currentData = $uploadDir . $oldData[$i];
        unlink($currentData);
    }
}

