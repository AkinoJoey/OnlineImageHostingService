<?php

namespace Helpers;

class ValidationHelper
{
    public static function validateFileType(string $mime) : bool{
        $allowedMimeTypes = ['image/png', 'image/jpg', 'image/gif'];
        return in_array($mime, $allowedMimeTypes);
    }


}