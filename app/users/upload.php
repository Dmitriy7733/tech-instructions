<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

// Проверяем, запущена ли сессия
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';

/*if (!isset($_SESSION['user_id'])) {
    //echo json_encode(['error' => 'Вы должны быть зарегистрированным пользователем для загрузки инструкций.']);
    //exit;
}*/
if (!isset($_SESSION['user_id'])) {
    // Предположим, что у нас есть пользователь с id = 2 (user) для тестирования
    $_SESSION['user_id'] = 2; // Замените на ID пользователя, которого хотите использовать для тестирования
}
// Проверяем, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, загрузился ли файл без ошибок
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Проверяем размер файла (например, ограничение в 5 МБ)
        $maxFileSize = 5 * 1024 * 1024; // 5 МБ
        if ($_FILES['file']['size'] > $maxFileSize) {
            $_SESSION['error'] = "Ошибка: Файл превышает максимальный размер 5 МБ.";
            header('Location: /index.php?page=upload');
            exit;
        }

        // Получаем информацию о загружаемом файле
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Проверяем допустимые расширения файлов
        $allowedfileExtensions = array('pdf', 'doc', 'docx', 'jpg', 'jpeg', 'bmp');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Создаем директорию uploads, если она не существует
            $uploadFileDir = __DIR__ . '/../../uploads/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            // Уникальное имя файла
            $newFileName = uniqid() . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            // Перемещаем файл в указанную директорию
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Получаем данные из формы
                $title = $_POST['title'];
                $description = $_POST['description'];
                $categoryId = $_POST['category_id'];
                $userId = $_SESSION['user_id'];

                // Сохраняем информацию о файле в базе данных
                $stmt = getDb()->prepare("INSERT INTO instructions (user_id, filename, category_id, subcategory_id, title, description) VALUES (:user_id, :filename, :category_id, :subcategory_id, :title, :description)");
                $stmt->execute([
                    ':user_id' => $userId,
                    ':filename' => $newFileName,
                    ':category_id' => $categoryId,
                    ':subcategory_id' => $_POST['subcategory_id'],
                    ':title' => $title, // Добавлено
                    ':description' => $description // Добавлено
                ]);

                $_SESSION['success'] = "Файл успешно загружен на одобрение.";
                header('Location: /index.php?page=upload');
                exit;
            } else {
                $_SESSION['error'] = "Ошибка при перемещении файла.";
                header('Location: /index.php?page=upload');
                exit;
            }
        } else {
            $_SESSION['error'] = "Недопустимый тип файла. Допустимые форматы: " . implode(', ', $allowedfileExtensions);
            header('Location: /index.php?page=upload');
            exit;
        }
    } else {
        $_SESSION['error'] = "Ошибка при загрузке файла.";
        header('Location: /index.php?page=upload');
        exit;
    }
} else {
    //$_SESSION['error'] = "Неверный запрос.";
    //header('Location: /index.php?page=upload');
    //exit;
}