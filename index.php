<?php
session_start();

require __DIR__ . '/configs/env.php';
require __DIR__ . '/models/BaseModel.php';
require __DIR__ . '/models/User.php';
require __DIR__ . '/controllers/AuthController.php';
require __DIR__ . '/controllers/HomeController.php';
require __DIR__ . '/controllers/AdminController.php';
require __DIR__ . '/routes/index.php';
