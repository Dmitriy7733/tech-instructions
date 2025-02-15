<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Устанавливаем страницу по умолчанию
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$role = $_SESSION['role'] ?? null;

// Проверяем, была ли выполнена аутентификация
$is_authenticated = isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated'];

switch ($page) {
    case 'register':
        if (!$is_authenticated) {
            include 'views/register.php';
        } else {
            // Если пользователь уже аутентифицирован, перенаправляем его на главную страницу или другую
            header('Location: index.php?page=home');
            exit;
        }
        break;
    case 'login':
        if (!$is_authenticated) {
            include 'views/login.php';
        } else {
            // Если пользователь уже аутентифицирован, перенаправляем его на главную страницу или другую
            header('Location: index.php?page=home');
            exit;
        }
        break;
    case 'admin':
        if ($is_authenticated && $role === 'admin') {
            include 'views/admin.php';
        } else {
            // Перенаправляем на страницу доступа запрещено
            header('Location: index.php?page=home');
            exit;
        }
        break;
    case 'upload_form':
        if ($is_authenticated || ($captcha_passed && !$is_authenticated)) {
            include 'views/upload_form.php';
        } else {
            // Перенаправляем на страницу доступа запрещено
            header('Location: index.php?page=home');
            exit;
        }
        break;  
    default:
        include 'views/home.php';
}

