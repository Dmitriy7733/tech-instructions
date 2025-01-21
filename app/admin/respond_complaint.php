<?php
//require_once "db.php";

try {
    $db = getDb();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['response'])) {
        $complaintId = intval($_POST['id']);
        $response = $_POST['response'];
        $stmt = $db->prepare("UPDATE complaints SET response = ?, response_status = 1 WHERE id = ?");
        $stmt->execute([$response, $complaintId]);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}