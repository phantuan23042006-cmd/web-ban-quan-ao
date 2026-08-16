<?php
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'admin', 'status' => 'active', 'full_name' => 'Admin User'];

require 'configs/env.php';
require 'configs/csrf.php';
require 'models/BaseModel.php';
require 'models/Product.php';
require 'models/Category.php';
require 'models/Review.php';
require 'models/Order.php';
require 'models/User.php';
require 'controllers/HomeController.php';

$controller = new HomeController();
ob_start();
$controller->index();
$output = ob_get_clean();
echo "Rendered storefront for Admin - Output Length: " . strlen($output) . "
";
