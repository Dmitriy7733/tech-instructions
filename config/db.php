<?php
function getDb() : PDO|null {
    static $db = null;
    if (is_null($db)) {
        $dbPath = __DIR__ . '/../instructions.db'; // Убедитесь, что путь правильный
        $db = new PDO('sqlite:' . $dbPath);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->exec("PRAGMA foreign_keys = ON");
    }
    return $db;
}