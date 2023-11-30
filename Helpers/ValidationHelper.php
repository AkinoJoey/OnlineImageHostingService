<?php

namespace Helpers;

class ValidationHelper
{
    public static function validateFileType(string $mime) : bool{
        $allowedMimeTypes = ['image/png', 'image/jpeg', 'image/gif'];
        return in_array($mime, $allowedMimeTypes);
    }

    public static function isFileSizeSmallerThan5MB(int $byteSize) : bool {
        $isSmallerThan5MB = $byteSize < (5 * 1024 * 1024); // 5MBをバイトに変換
        return $isSmallerThan5MB;
    }

    public static function checkUploadSizeLimit(int $totalUploadSizeToday,int $uploadedSize) : bool {
        $limit = (5 * 1024 * 1024);  //5MBが上限
        return ($uploadedSize + $totalUploadSizeToday) < $limit;
    }

}