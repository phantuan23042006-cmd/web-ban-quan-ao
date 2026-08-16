<?php
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'user', 'status' => 'active', 'full_name' => 'Nguyen Trong Tan'];

require 'configs/env.php';
require 'configs/csrf.php';
require 'models/BaseModel.php';
require 'models/User.php';
require 'controllers/AuthController.php';

echo "Before Logout SESSION user: " . (isset($_SESSION['user']) ? 'EXISTS' : 'EMPTY') . "
";

$auth = new AuthController();
try {
    $auth->logout();
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "
";
}

echo "After Logout SESSION user: " . (isset($_SESSION['user']) ? 'EXISTS' : 'EMPTY') . "
";
echo "After Logout SESSION success_message: " . ($_SESSION['success_message'] ?? 'NONE') . "
";
