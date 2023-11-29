<?php

namespace Helpers;

class ValidationHelper
{
    public static function validateFileType(string $mime) : bool{
        $allowedMimeTypes = ['image/png', 'image/jpeg', 'image/gif'];
        return in_array($mime, $allowedMimeTypes);
    }

    public static function isFileSizeSmallerThan5MB(string $filePath) : bool {
        $fileSize = filesize($filePath);
        $isSmallerThan5MB = $fileSize < (5 * 1024 * 1024); // 5MBをバイトに変換
        return $isSmallerThan5MB;
    }


}