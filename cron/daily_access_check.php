<?php
# cronでは作業ディレクトリがホームディレクトリになるから、作業ディレクトリをcronディレクトリに変更
chdir( __DIR__  . "/..");

// DailyAccessCheckコマンドを実行する
$output = [];
exec("php console dac", $output);

print_r($output);