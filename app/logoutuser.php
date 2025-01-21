
<?php
session_start();
session_destroy(); // Завершить сессию
header("Location: index.php"); // Перенаправить на главную страницу
exit();
