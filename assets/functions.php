<?php

function getCategories($asJson = false) {
    $stmt = getDb()->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($asJson) {
        return json_encode($categories);
    }
    
    return $categories;
}
function getAdminInstructions($approved = 0) {
    // Подготовленный запрос для получения неодобренных инструкций
    $stmt = getDb()->prepare("
        SELECT i.*, u.username, c1.name AS category_name, c2.name AS subcategory_name 
        FROM instructions AS i 
        LEFT JOIN users AS u ON i.user_id = u.id 
        LEFT JOIN categories AS c1 ON i.category_id = c1.id 
        LEFT JOIN categories AS c2 ON i.subcategory_id = c2.id 
        WHERE i.approved = :approved
    ");
    $stmt->bindParam(':approved', $approved, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
//получение зарегистрированных пользователей
function getUsers(): array {
    $db = getDb();
    if ($db === null) {
        throw new Exception("Unable to connect to the database.");
    }
    
    $stmt = $db->prepare("SELECT * FROM users WHERE role != 'admin'");
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

