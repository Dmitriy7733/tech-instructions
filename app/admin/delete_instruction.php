<?php
//require_once "db.php";

try {
    $db = getDb();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $instructionId = intval($_POST['id']);
        $stmt = $db->prepare("DELETE FROM instructions WHERE id = ?");
        $stmt->execute([$instructionId]);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}