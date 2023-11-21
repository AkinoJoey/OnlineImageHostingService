<?php

namespace Helpers;

use Database\MySQLWrapper;
use Exception;

class DatabaseHelper{
    public static function insertImageData(string $path): bool{
        $mysqli = new MySQLWrapper();
        $query = "INSERT INTO images (path, shared_url, delete_url, view_count) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ss", $path, );
        $result = $stmt->execute();

        if (!$result) throw new Exception("Error executing INSERT query: " . $stmt->error);

        return true;
    }

}