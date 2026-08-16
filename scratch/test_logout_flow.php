<?php
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'user', 'status' => 'active', 'full_name' => 'Test User'];

require 'configs/env.php';
require 'configs/csrf.php';
require 'models/BaseModel.php';
require 'models/User.php';
require 'controllers/AuthController.php';

// Simulate route logout execution
if (!empty($_SESSION['user'])) {
    $userId = (int) ($_SESSION['user']['id'] ?? 0);
    $userExists = (new User())->findById($userId);
}

$auth = new AuthController();
// Check if logout redirects properly
try {
    $auth->logout();
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "
";
}
