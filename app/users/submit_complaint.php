<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Временно устанавливаем user_id для тестирования
$_SESSION['user_id'] = 2;
require_once __DIR__ . '/../../config/db.php';
// Проверка, что данные были отправлены
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $instructionId = isset($_POST['instruction_id']) ? intval($_POST['instruction_id']) : 0;
    $complaintText = isset($_POST['complaint_text']) ? trim($_POST['complaint_text']) : '';
    $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0; // Получаем user_id

    // Проверка на пустые значения
    if ($instructionId <= 0 || empty($complaintText) || $userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Некорректные данные.']);
        exit;
    }

    // Подготовка запроса для вставки жалобы в базу данных
    $stmt = $pdo->prepare("INSERT INTO complaints (instruction_id, user_id, complaint_text) VALUES (?, ?, ?)");
    $result = $stmt->execute([$instructionId, $userId, $complaintText]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Не удалось отправить жалобу. Попробуйте позже.']);
    }
}

/*if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['instruction_id'], $_POST['complaint_text'])) {
        $instructionId = intval($_POST['instruction_id']);
        $complaintText = htmlspecialchars($_POST['complaint_text']);
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        if ($userId === null) {
            echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован.']);
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
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса.']);
}*/