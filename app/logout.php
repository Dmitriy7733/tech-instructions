<?php
session_start();

// Уничтожаем все данные сессии
$_SESSION = array(); // Очищаем массив сессии
session_destroy(); // Уничтожаем сессию

// Перенаправляем пользователя на страницу входа или главную страницу
header("Location: /index.php"); // Замените на нужный URL
exit();
