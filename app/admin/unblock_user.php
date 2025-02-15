<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/db.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $db = getDb();
    $stmt = $db->prepare("UPDATE users SET status = 'active' WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'ID not provided']);
}
