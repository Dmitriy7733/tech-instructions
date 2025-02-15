<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/db.php';
$pdo = getDb();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchTerm = isset($_POST['search_term']) ? trim($_POST['search_term']) : '';
    error_log("Searching for: " . $searchTerm);

    if (empty($searchTerm)) {
        echo json_encode([]);
        exit;
    }

    if (isset($_SESSION['search_cache'][$searchTerm])) {
        error_log("Cache hit for: " . $searchTerm);
        echo json_encode($_SESSION['search_cache'][$searchTerm]);
        exit;
    }
// Подготовленное выражение для поиска с условием по одобренным инструкциям
$stmt = $pdo->prepare("SELECT * FROM instructions WHERE (title LIKE :searchTerm OR description LIKE :searchTerm) AND approved = 1");
$stmt->execute(['searchTerm' => '%' . $searchTerm . '%']);
    // Подготовленное выражение для поиска
    /*$stmt = $pdo->prepare("SELECT * FROM instructions WHERE title LIKE :searchTerm OR description LIKE :searchTerm");
    $stmt->execute(['searchTerm' => '%' . $searchTerm . '%']);*/
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("Results found: " . print_r($results, true));

    $_SESSION['search_cache'][$searchTerm] = $results;

    echo json_encode($results);
}

