<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userCaptcha = $_POST['captcha'] ?? '';
    
    if (isset($_SESSION['captcha']) && $userCaptcha === $_SESSION['captcha']) {
        // Капча верна, продолжайте обработку запроса
        echo json_encode(['success' => true, 'message' => 'Капча верна']);
    } else {
        // Капча неверна
        echo json_encode(['success' => false, 'message' => 'Неверная капча']);
    }
}