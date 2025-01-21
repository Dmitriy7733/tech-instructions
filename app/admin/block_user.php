<?php
//require_once "db.php";

try {
    $db = getDb();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['reason'])) {
        $userId = intval($_POST['id']);
        $reason = $_POST['reason'];
        $stmt = $db->prepare("UPDATE users SET status = 'blocked', block_reason = ? WHERE id = ?");
        $stmt->execute([$reason, $userId]);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}