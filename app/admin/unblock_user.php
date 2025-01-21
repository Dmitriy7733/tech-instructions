<?php
//require_once "db.php";

try {
    $db = getDb();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $userId = intval($_POST['id']);
        $stmt = $db->prepare("UPDATE users SET status = 'active', block_reason = NULL WHERE id = ?");
        $stmt->execute([$userId]);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}