<?php
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'admin', 'status' => 'active', 'full_name' => 'Admin User'];

$_GET['action'] = 'admin-orders';
$_GET['from_date'] = '2026-08-01';
$_GET['to_date'] = '2026-08-31';

require 'configs/env.php';
require 'configs/csrf.php';
require 'models/BaseModel.php';
require 'models/User.php';
require 'models/Category.php';
require 'models/Product.php';
require 'models/ProductDetail.php';
require 'models/Supplier.php';
require 'models/Review.php';
require 'models/Order.php';
require 'controllers/AdminController.php';

$admin = new AdminController();
ob_start();
$admin->orders();
$output = ob_get_clean();
echo "Rendered Admin Orders Date Filter - Output Length: " . strlen($output) . "
";
