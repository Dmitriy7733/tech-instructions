<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['instruction_id'], $_POST['complaint_text'])) {
        $instructionId = intval($_POST['instruction_id']);
        $complaintText = htmlspecialchars($_POST['complaint_text']);
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        if ($userId === null) {
            echo json_encode(['success' => false, 'message' => 'Для отправки жалобу авторизуйтесь пожалуйста!']);
            exit;
        }

        // Проверка существования инструкции
        $stmtCheck = getDb()->prepare("SELECT COUNT(*) FROM instructions WHERE id = :instruction_id");
        $stmtCheck->execute([':instruction_id' => $instructionId]);
        $exists = $stmtCheck->fetchColumn();
        if ($exists == 0) {
            echo json_encode(['success' => false, 'message' => 'Инструкция не найдена. Запрашиваемый ID: ' . $instructionId]);
            exit;
        }

        // Проверка существования пользователя
        $stmtUserCheck = getDb()->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
        $stmtUserCheck->execute([':user_id' => $userId]);
        $userExists = $stmtUserCheck->fetchColumn();
        if ($userExists == 0) {
            echo json_encode(['success' => false, 'message' => 'Пользователь не найден.']);
            exit;
        }

        try {
            $stmt = getDb()->prepare("INSERT INTO complaints (instruction_id, user_id, complaint_text) VALUES (:instruction_id, :user_id, :complaint_text)");
            $stmt->execute([
                ':instruction_id' => $instructionId,
                ':user_id' => $userId,
                ':complaint_text' => $complaintText
            ]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Недостаточно данных.']);
    }
} /*else {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса.']);//причина не понятна при закоментировании Касперский засыпает скриптами
}*/

