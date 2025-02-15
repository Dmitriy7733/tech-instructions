<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Инструкции для техники</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        
body {
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Это позволит футеру оставаться внизу */
    min-height: 100vh;
    background-color: #f8f9fa;
    overflow-x: hidden; /* Скрыть горизонтальный скроллинг */
}

.container {
    max-width: 100%;
    padding: 10 10px; 
    box-sizing: border-box;
}

        .table-responsive {
            overflow-x: auto; /* Позволить прокрутку таблицы, если она слишком широка */
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .register-container {
            max-width: 400px;
            margin: auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        nav {
            width: 100%;
        }
        footer {
            width: 100%;
            position: relative;
            bottom: 0;
        }
        @media (max-width: 500px) {
    .modal-dialog {
        margin: 5; /* Убираем отступы для мобильных устройств */
        width: 100%; /* Устанавливаем ширину на 100% */
        max-width: 90%; /* Ограничиваем ширину до 100% */
    }
    
    .modal-content {
        border-radius: 0; /* Убираем закругления для мобильных устройств */
    }

    .btn {
        width: 100%; /* Кнопки занимают всю ширину */
        margin-bottom: 10px; /* Отступ между кнопками */
    }
    .modal-content .btn {
    width: auto; /* Изменяем ширину для кнопок в модальном окне */
}

    .modal-body {
        overflow-y: auto; /* Позволяет прокручивать содержимое */
        max-height: 70vh; /* Ограничиваем высоту модального окна */
    }
}
    </style>
</head>
<body>

<!-- Навигационная панель -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="/includes/free-icon-appliances.png" alt="Логотип" style="height: 40px; margin-right: 10px;">
            Инструкции для техники
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="?page=home">Главная</a></li>
                <li class="nav-item"><a class="nav-link" href="?page=home#about">О сайте</a></li>
                <li class="nav-item"><a class="nav-link" href="app/logout.php">Выход</a></li>
                <li class="nav-item"><a class="nav-link" href="?page=register">Регистрация</a></li>
                <li class="nav-item"><a class="nav-link" href="?page=login">Вход</a></li>
                <li class="nav-item"><a class="nav-link" href="?page=upload_form">Добавить инструкцию</a></li>
            </ul>
        </div>
    </div>
</nav>