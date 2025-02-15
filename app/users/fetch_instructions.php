<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subcategory_id'])) {
    $subcategoryId = (int)$_POST['subcategory_id']; // Приводим к целому числу

    if ($subcategoryId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Некорректный ID подкатегории.']);
        exit;
    }

    try {
        // Подключаемся к базе данных
        $stmt = getDb()->prepare("SELECT id, filename, title, description FROM instructions WHERE subcategory_id = :subcategory_id AND approved = 1");
        $stmt->execute([':subcategory_id' => $subcategoryId]);
        $instructions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'instructions' => $instructions]);
    } catch (Exception $e) {
        error_log($e->getMessage()); // Логируем ошибку
        echo json_encode(['success' => false, 'message' => 'Ошибка при выполнении запроса.']);
    }
}