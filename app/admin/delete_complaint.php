<?php
//require_once "db.php";

try {
    $db = getDb();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $complaintId = intval($_POST['id']);
        $stmt = $db->prepare("DELETE FROM complaints WHERE id = ?");
        $stmt->execute([$complaintId]);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}