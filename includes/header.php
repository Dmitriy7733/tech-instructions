<?php
session_start(); // Не забудьте запустить сессию, если вы используете $_SESSION
?>
<!-- header.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Инструкции для техники</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Навигационная панель -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" style="height: 40px;">Инструкции для техники</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="?page=home">Главная</a></li>
            <li class="nav-item"><a class="nav-link" href="?page=home#about">О сайте</a></li>
            <?php if (isset($_SESSION['registered']) && $_SESSION['registered']): ?>
                <li class="nav-item"><a class="nav-link" href="logoutuser.php">Выход</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="?page=register">Регистрация</a></li>
                <li class="nav-item"><a class="nav-link" href="?page=login">Вход</a></li>
                <li class="nav-item"><a class="nav-link" href="?page=upload">Добавить инструкцию</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>