<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include './../../config/db.php';
header('Content-Type: application/json');

try {
    $stmt = getDb()->query("SELECT id, name FROM categories WHERE parent_id IS NULL");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($categories === false) {
        throw new Exception('Ошибка выполнения запроса');
    }

    echo json_encode($categories);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка получения категорий: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}