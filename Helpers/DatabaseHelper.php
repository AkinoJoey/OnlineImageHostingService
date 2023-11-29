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
        $mysqli = new MySQLWrapper();
        $stmt = $mysqli->prepare("SELECT path, mime ,view_count FROM images WHERE shared_url = ?");
        $stmt->bind_param('s', $shared_url);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if (!$data) return false;

        return $data;
    }

    public static function getSharedUrl(string $delete_url) : string | false {
        $mysqli = new MySQLWrapper();
        $stmt = $mysqli->prepare("SELECT shared_url FROM images WHERE delete_URL = ?");
        $stmt->bind_param('s', $delete_url);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if (!$data) return false;

        return $data['shared_url'];
    }

    public static function deleteImageData(string $shared_url) : bool {
        $mysqli = new MySQLWrapper();
        $stmt = $mysqli->prepare("DELETE FROM images WHERE shared_url = ?");
        $stmt-> bind_param('s', $shared_url);
        $stmt->execute();
        $rowsAffected = $stmt->affected_rows;

        return $rowsAffected > 0;
    }

    public static function updateImageData(string $shared_url) : bool {
        $mysqli = new MySQLWrapper();
        $currentTime = date('Y-m-d H:i:s');
        $stmt = $mysqli->prepare("UPDATE images SET view_count = view_count + 1, last_accessed_at = ?  WHERE shared_url = ? ");
        $stmt->bind_param('ss', $currentTime ,$shared_url);
        $stmt->execute();
        $rowsAffected = $stmt->affected_rows;

        return $rowsAffected > 0;
    }

    public static function deleteInactiveImageData30Days() : void{
        $mysqli = new MySQLWrapper();
        $deleteThreshold = date('Y-m-d H:i:s', strtotime('-1 days')); 
        $stmt = $mysqli->prepare("DELETE FROM images WHERE last_accessed_at < ?");
        $stmt->bind_param('s', $deleteThreshold);
        $stmt->execute();

        if ($stmt->errno) {
            $errorMessage = "Error: {$stmt->error}";
            error_log($errorMessage);
        }
    }

}