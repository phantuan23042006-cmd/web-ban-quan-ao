<?php
require 'configs/env.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USERNAME, DB_PASSWORD);

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "TABLES FOUND: " . implode(', ', $tables) . "

";

foreach ($tables as $table) {
    echo "=== TABLE: $table ===
";
    $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    echo $create['Create Table'] . "

";
}
