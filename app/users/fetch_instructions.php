<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../config/db.php';

//header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subcategory_id'])) {
    $subcategoryId = $_POST['subcategory_id'];

    // Получаем инструкции по подкатегории
    $stmt = getDb()->prepare("SELECT filename, title, description FROM instructions WHERE subcategory_id = :subcategory_id AND approved = 1");
    $stmt->execute([':subcategory_id' => $subcategoryId]);
    $instructions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($instructions); // Возвращаем данные в формате JSON
    exit; // Завершаем выполнение скрипта
} else {
    // Если subcategory_id не передан, возвращаем пустой массив
    echo json_encode(['instructions' => []]);
}
