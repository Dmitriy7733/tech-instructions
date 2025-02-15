<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/db.php';
try {
    $db = getDb();

    // Извлечение жалоб из базы данных
    $stmt = $db->query("SELECT * FROM complaints");
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

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