<?php
require 'configs/env.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USERNAME, DB_PASSWORD);
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);
$cols = $pdo->query('DESCRIBE users')->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
