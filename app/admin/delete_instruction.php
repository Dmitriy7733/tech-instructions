<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/db.php';
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Получаем информацию о инструкции для последующего удаления файла
    $stmt = getDb()->prepare("SELECT filename FROM instructions WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $instruction = $stmt->fetch(PDO::FETCH_ASSOC);

    // Удаляем инструкцию
    if ($instruction) {
        // Удаляем файл из папки uploads
        $filePath = __DIR__ . '/../../uploads/' . $instruction['filename'];
        if (file_exists($filePath)) {
            if (unlink($filePath)) { // Удаляем файл и проверяем успешность
                // Файл успешно удалён
            } else {
                echo json_encode(['error' => 'Ошибка при удалении файла.']);
                exit;
            }
        } else {
            echo json_encode(['error' => 'Файл не найден.']);
            exit;
        }

        // Удаляем запись из базы данных
        $deleteStmt = getDb()->prepare("DELETE FROM instructions WHERE id = :id");
        $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $deleteStmt->execute();

        echo json_encode(['success' => 'Instruction deleted successfully.']);
    } else {
        echo json_encode(['error' => 'Instruction not found.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
