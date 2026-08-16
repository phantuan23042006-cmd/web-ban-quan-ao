<?php
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'user', 'status' => 'active', 'full_name' => 'Nguyen Trong Tan'];

require 'configs/env.php';
require 'configs/csrf.php';
require 'models/BaseModel.php';
require 'models/User.php';
require 'controllers/AuthController.php';

$auth = new AuthController();
$auth->logout();
