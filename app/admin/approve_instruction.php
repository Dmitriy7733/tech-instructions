<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/db.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Обновляем статус одобрения инструкции
    $stmt = getDb()->prepare("UPDATE instructions SET approved = 1 WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Instruction approved successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to approve instruction.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}