<?php
require 'configs/env.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USERNAME, DB_PASSWORD);
$pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_email (email),
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
echo "Table password_resets created successfully.
";
