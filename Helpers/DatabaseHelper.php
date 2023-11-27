<?php

namespace Helpers;

use Database\MySQLWrapper;
use Exception;

class DatabaseHelper{
    public static function insertImageData(string $path, string $shared_url, string $delete_url, string $mime): bool{
        $mysqli = new MySQLWrapper();
        $query = "INSERT INTO images (path, shared_url, delete_url, mime) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssss",$path, $shared_url, $delete_url, $mime);
        $result = $stmt->execute();

        if (!$result) throw new Exception("Error executing INSERT query: " . $stmt->error);

        return true;
    }

    public static function getImageData(string $shared_url): Array | false{
        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT path, mime ,view_count FROM images WHERE shared_url = ?");
        $stmt->bind_param('s', $shared_url);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if (!$data) return false;

        return $data;
    }

}