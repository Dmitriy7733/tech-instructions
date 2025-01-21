<?php
require_once __DIR__ . '/../config/db.php';
function getCategories($asJson = false) {
    $stmt = getDb()->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($asJson) {
        return json_encode($categories);
    }
    
    return $categories;
}

// Функция для получения всех одобренных инструкций по выбранной подкатегории
function getApprovedInstructions($subcategoryId) {
    $stmt = getDb()->prepare("SELECT * FROM instructions WHERE category_id = :category_id AND approved = 1");
    $stmt->execute([':category_id' => $subcategoryId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Добавим код для временного одобрения всех инструкций
function approveAllInstructions() {
    $stmt = getDb()->prepare("UPDATE instructions SET approved = 1");
    return $stmt->execute();
}

// Временно одобряем все инструкции (только для тестирования)
approveAllInstructions();

// Обработка выбора подкатегории
if (isset($_GET['subcategory_id'])) {
    $subcategoryId = $_GET['subcategory_id'];
    $instructions = getApprovedInstructions($subcategoryId);
} else {
    $instructions = [];
}
