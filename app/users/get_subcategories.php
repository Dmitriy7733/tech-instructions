<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if (isset($_GET['category_id'])) {
    $categoryId = intval($_GET['category_id']);
    
    $stmt = getDb()->prepare("SELECT * FROM categories WHERE parent_id = :category_id");
    $stmt->execute([':category_id' => $categoryId]);
    $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($subcategories);
} else {
    echo json_encode(['error' => 'Не передан идентификатор категории.']);
}