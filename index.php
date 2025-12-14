<?php

header('Content-Type: text/html; charset=UTF-8');

session_start();

date_default_timezone_set('Europe/Minsk');

require_once __DIR__ . '/includes/autoload.php';

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Убираем query string и базовый путь
$path = parse_url($requestUri, PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

// Простой роутинг
$routes = [
    'GET' => [
        '/' => function() {
            view('index');
        },
        '/login' => function() {
            (new AuthController())->showLogin();
        },
        '/register' => function() {
            (new AuthController())->showRegister();
        },
        '/verify-email' => function() {
            (new AuthController())->showVerifyEmail();
        },
        '/forgot-password' => function() {
            (new PasswordController())->showForgotPassword();
        },
        '/reset-password' => function() {
            (new PasswordController())->showResetPassword();
        },
        '/new-password' => function() {
            (new PasswordController())->showNewPassword();
        },
        '/profile' => function() {
            (new ProfileController())->showProfile();
        },
        '/profile/edit' => function() {
            (new ProfileController())->showEdit();
        },
        '/admin' => function() {
            (new AdminController())->index();
        },
        '/logout' => function() {
            (new AuthController())->logout();
        }
    ],
    'POST' => [
        '/login' => function() {
            (new AuthController())->login();
        },
        '/register' => function() {
            (new AuthController())->register();
        },
        '/verify-email' => function() {
            (new AuthController())->verifyEmail();
        },
        '/forgot-password' => function() {
            (new PasswordController())->forgotPassword();
        },
        '/reset-password' => function() {
            (new PasswordController())->verifyResetCode();
        },
        '/new-password' => function() {
            (new PasswordController())->setNewPassword();
        },
        '/profile/update' => function() {
            (new ProfileController())->update();
        },
        '/profile/change-password' => function() {
            (new ProfileController())->changePassword();
        },
        '/admin/toggle-user' => function() {
            (new AdminController())->toggleUserStatus();
        },
        '/admin/assign-role' => function() {
            (new AdminController())->assignRole();
        }
    ]
];

// Обработка маршрута
if (isset($routes[$requestMethod][$path])) {
    try {
        $routes[$requestMethod][$path]();
    } catch (Exception $e) {
        http_response_code(500);
        echo "Ошибка: " . htmlspecialchars($e->getMessage());
    }
} else {
    http_response_code(404);
    echo "Страница не найдена";
}

