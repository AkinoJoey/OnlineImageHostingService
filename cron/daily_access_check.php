<?php
# cronでは作業ディレクトリがホームディレクトリになるから、作業ディレクトリをcronディレクトリに変更
chdir( __DIR__  . "/..");
date_default_timezone_set('Asia/Tokyo');
echo date("Y-m-d H:i:s" . "\n");

$output = [];
// DailyAccessCheckコマンドを実行する
$result = exec("php console dac", $output);

if($result) print_r($output);
