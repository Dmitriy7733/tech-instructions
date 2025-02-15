<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include './../../config/db.php';

// Проверяем, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, загрузился ли файл без ошибок
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Проверяем размер файла (например, ограничение в 5 МБ)
        $maxFileSize = 5 * 1024 * 1024; // 5 МБ
        if ($_FILES['file']['size'] > $maxFileSize) {
            $_SESSION['error'] = "Ошибка: Файл превышает максимальный размер 5 МБ.";
            header('Location: /index.php?page=upload_form');
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
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Сохраняем информацию о файле в базе данных
$stmt = getDb()->prepare("INSERT INTO instructions (user_id, filename, category_id, subcategory_id, title, description) VALUES (:user_id, :filename, :category_id, :subcategory_id, :title, :description)");
$params = [
    ':filename' => $newFileName,
    ':category_id' => $categoryId,
    ':subcategory_id' => $_POST['subcategory_id'],
    ':title' => $title,
    ':description' => $description
];

if ($userId !== null) {
    $params[':user_id'] = $userId;
} else {
    $stmt = getDb()->prepare("INSERT INTO instructions (filename, category_id, subcategory_id, title, description) VALUES (:filename, :category_id, :subcategory_id, :title, :description)");
}

try {
    $stmt->execute($params);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "Ошибка базы данных: " . $e->getMessage();
    header('Location: /index.php?page=upload_form');
    exit;
}
                $_SESSION['success'] = "Файл успешно загружен на одобрение.";
                header('Location: /index.php?page=upload_form');
                exit;
            } else {
                $_SESSION['error'] = "Ошибка при перемещении файла.";
                header('Location: /index.php?page=upload_form');
                exit;
            }
        } else {
            $_SESSION['error'] = "Недопустимый тип файла. Допустимые форматы: " . implode(', ', $allowedfileExtensions);
            header('Location: /index.php?page=upload_form');
            exit;
        }
    } else {
        $_SESSION['error'] = "Ошибка при загрузке файла.";
        header('Location: /index.php?page=upload_form');
        exit;
    }
} else {
    $_SESSION['error'] = "Неверный запрос.";
    header('Location: /index.php?page=upload_form');
    exit;
}