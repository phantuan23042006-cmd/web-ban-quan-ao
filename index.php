<?php
session_start();

require __DIR__ . '/configs/env.php';
require __DIR__ . '/configs/csrf.php';
require __DIR__ . '/models/BaseModel.php';
require __DIR__ . '/models/User.php';
require __DIR__ . '/models/Category.php';
require __DIR__ . '/models/Supplier.php';
require __DIR__ . '/models/Product.php';
require __DIR__ . '/models/ProductDetail.php';
require __DIR__ . '/models/Review.php';
require __DIR__ . '/models/Cart.php';
require __DIR__ . '/models/Order.php';
require __DIR__ . '/controllers/AuthController.php';
require __DIR__ . '/controllers/HomeController.php';
require __DIR__ . '/controllers/AdminController.php';
require __DIR__ . '/routes/index.php';
