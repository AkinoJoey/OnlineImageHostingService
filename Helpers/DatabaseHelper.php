<?php

namespace Helpers;

use Database\MySQLWrapper;
class DatabaseHelper{
    public static function insertImageData(string $path, int $byteSize, string $shared_url, string $delete_url, string $mime, string $ipAddress): bool{
        $mysqli = new MySQLWrapper();
        $query = "INSERT INTO images (path, byte_size ,shared_url, delete_url, mime, uploaded_ip_address) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sissss",$path, $byteSize, $shared_url, $delete_url, $mime, $ipAddress);
        $result = $stmt->execute();

        return $result;
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

        if(!$data) return false;

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
        $deleteThreshold = date('Y-m-d H:i:s', strtotime('-30 day'));
        $stmt = $mysqli->prepare("DELETE FROM images WHERE last_accessed_at < ?");
        $stmt->bind_param('s', $deleteThreshold);
        $stmt->execute();

        if ($stmt->errno) {
            $errorMessage = "Error: {$stmt->error}";
            error_log($errorMessage);
        }
    }

    public static function getInactiveImageData30Days() : array {
        $mysqli = new MySQLWrapper();
        $deleteThreshold = date('Y-m-d H:i:s', strtotime('-30 day')); 
        $stmt = $mysqli->prepare("SELECT path FROM images WHERE last_accessed_at < ?");
        $stmt->bind_param('s', $deleteThreshold);
        $stmt->execute();

        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row['path'];
        }
    
        return $data;
    }

    public static function getTotalUploadSizeToday(string $ipAddress) : int | false {
        $mysqli = new MySQLWrapper();
        $stmt = $mysqli->prepare("SELECT SUM(byte_size) as total_size FROM images WHERE uploaded_ip_address = ? AND DATE(created_at) = CURDATE()");
        $stmt->bind_param('s', $ipAddress);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if($row['total_size'] === null){
            return 0;
        }elseif(!$row){
            return false;
        }

        return $row['total_size'];
    }

}