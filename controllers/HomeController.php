<?php

class HomeController
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | BẮT BUỘC ĐĂNG NHẬP
        |--------------------------------------------------------------------------
        */

        if (empty($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN KHÔNG DÙNG GIAO DIỆN USER
        |--------------------------------------------------------------------------
        */

        if (($_SESSION['user']['role'] ?? '') === 'admin') {
            header(
                'Location: ' .
                BASE_URL .
                '?action=admin-dashboard'
            );
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | GIAO DIỆN TRANG CHỦ USER
        |--------------------------------------------------------------------------
        */

        $title = 'Trang chủ';
        $view = 'home';
        $layout = 'user';

        $currentUser = $_SESSION['user'];

        $successMessage =
            $_SESSION['success_message'] ?? null;

        unset($_SESSION['success_message']);

        require PATH_VIEW_MAIN;
    }
}