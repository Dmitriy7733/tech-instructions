<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/db.php';
$error = ''; // Инициализация переменной для ошибок

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';

    try {
        // Проверка существования пользователя
        $stmt = getDb()->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Получаем ассоциативный массив

        // Если пользователь найден
        if ($user) {
            // Проверка пароля
            if (password_verify($password, $user['password'])) {
                // Успешная аутентификация, сохраняем роль и ID пользователя в сессии
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['is_authenticated'] = true;
                
                // Перенаправление в зависимости от роли
                if ($user['role'] === 'admin') {
                    header('Location: index.php?page=admin');
                } elseif ($user['role'] === 'user') {
                    header('Location: index.php?page=home');
                }
                exit();
            } else {
                $error = "Неправильный пароль.";
            }
        } else {
            $error = "Пользователь не найден.";
        }
    } catch (PDOException $e) {
        // Логирование ошибки
        error_log($e->getMessage());
        $error = "Ошибка подключения к базе данных.";
    }
}

// Если пользователь уже аутентифицирован, перенаправляем на соответствующую страницу
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: index.php?page=admin');
    } elseif ($_SESSION['role'] === 'user') {
        header('Location: index.php?page=home');
    }
    exit();
}

// Если есть ошибка, сохраняем её в сессии
if ($error) {
    $_SESSION['error'] = $error;
}

// Отображаем ошибки из сессии
if (isset($_SESSION['error'])) {
    echo "<p style='color:red;'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']); // Удаляем ошибку после отображения
}
?>
<?php include 'includes/header.php'; ?>
<div class="login-container">
    <h1 class="text-center">Вход в систему</h1>
    <form action="" method="post">
        <div class="form-group">
            <label for="username">Имя пользователя:</label>
            <input type="text" name="username" id="username" class="form-control" required autocomplete="username">
        </div>
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Войти</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>