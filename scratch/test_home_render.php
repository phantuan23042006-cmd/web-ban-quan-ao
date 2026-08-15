<?php
$_SESSION['user'] = ['id' => 1, 'role' => 'user', 'status' => 'active'];
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
echo "Length of output: " . strlen($output) . "
";
