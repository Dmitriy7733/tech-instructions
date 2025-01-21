<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

try {
    $stmt = getDb()->query("SELECT id, name FROM categories WHERE parent_id IS NULL");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($categories);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка получения категорий: ' . $e->getMessage()]);
}