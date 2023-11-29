<?php

namespace Database\Migrations;

use Database\SchemaMigration;

class CreateImageTable implements SchemaMigration
{
    public function up(): array
    {
        return [
            'CREATE TABLE IF NOT EXISTS images(
                id INT PRIMARY KEY AUTO_INCREMENT, 
                path VARCHAR(255) NOT NULL UNIQUE,
                shared_url VARCHAR(255) NOT NULL UNIQUE,
                delete_url VARCHAR(255) NOT NULL UNIQUE,
                mime VARCHAR(20) NOT NULL,
                view_count INT DEFAULT 0,
                last_accessed_at DATETIME DEFAULT CURRENT_TIMESTAMP);'
        ];
    }

    public function down(): array
    {
        return [
            'DROP TABLE images'
        ];
    }
}