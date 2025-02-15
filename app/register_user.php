<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    // Подключаемся к базе данных
    require './../config/db.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Получаем данные из формы
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        $captcha = trim($_POST['captcha']);
        
        // Валидация данных
        $errors = [];
        
        // Проверка длины логина
        if (strlen($username) < 3 || strlen($username) > 20) {
            $errors[] = "Логин должен содержать от 3 до 20 символов.";
        }
        
        // Проверка на запрещенные символы в логине
        if (!preg_match('/^[a-zA-Zа-яА-Я0-9_]+$/u', $username)) {
            $errors[] = "Логин может содержать только буквы, цифры и символ '_'.";
        }
    
        // Проверка длины пароля
        if (strlen($password) < 6 || strlen($password) > 20) {
            $errors[] = "Пароль должен содержать от 6 до 20 символов.";
        }
    
        // Проверка совпадения паролей
        if ($password !== $confirm_password) {
            $errors[] = "Пароли не совпадают.";
        }
    
        // Проверка на существование пользователя
        $stmt = getDb()->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "Пользователь с таким логином или электронной почтой уже существует.";
        }
    
        // Если есть ошибки, выводим их
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Вставляем пользователя в базу данных
            $stmt = getDb()->prepare("INSERT INTO users (username, password, email, status, created_at, role) VALUES (:username, :password, :email, 'active', datetime('now'), 'user')");
            if ($stmt->execute([':username' => $username, ':password' => $hashed_password, ':email' => $email])) {
                echo "<div class='alert alert-success'>Регистрация прошла успешно!</div>";
                header("Location: /index.php?page=login"); // перенаправление на страницу входа
                exit();
            } else {
                echo "<div class='alert alert-danger'>Ошибка при регистрации. Попробуйте снова.</div>";
            }
        }
    } else {
        // Если не POST запрос, перенаправляем на форму регистрации
        header("Location:/index.php?page=register");
        exit();
    }