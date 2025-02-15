<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/db.php';//только так получается подключиться
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = getDb()->prepare("SELECT filename FROM instructions WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $instruction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($instruction) {
        $filePath = __DIR__ . '/../../uploads/' . $instruction['filename']; // Использование абсолютного пути
        if (file_exists($filePath)) {
            echo json_encode(['filePath' => '/uploads/' . htmlspecialchars($instruction['filename'])]);
        } else {
            echo json_encode(['error' => 'File not found']);
        }
    } else {
        echo json_encode(['error' => 'Instruction not found']);
    }
} 




