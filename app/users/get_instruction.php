<?php

require_once __DIR__ . '/../../config/db.php';

//header('Content-Type: application/json');
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = getDb()->prepare("SELECT name, filename FROM instructions WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $instruction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($instruction) {
        echo json_encode([
            'text' => "Инструкция: " . $instruction['name'],
            'downloadable' => true,
            'filename' => $instruction['filename']
        ]);
    } else {
        echo json_encode([
            'text' => 'Инструкция не найдена.',
            'downloadable' => false
        ]);
    }
} else {
    echo json_encode([
        'text' => 'ID инструкции не указан.',
        'downloadable' => false
    ]);
}
