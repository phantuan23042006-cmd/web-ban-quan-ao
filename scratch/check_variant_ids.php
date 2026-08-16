<?php
require 'configs/env.php';
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USERNAME, DB_PASSWORD);
$variants = $pdo->query("SELECT id, san_pham_id, size, so_luong, gia_ban FROM chi_tiet_san_pham")->fetchAll(PDO::FETCH_ASSOC);
print_r($variants);
