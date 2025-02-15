<?php
function getDb() : PDO|null {
    static $db = null;
    if (is_null($db)) {
        $dbPath = dirname(__DIR__, 1) . '/instructions.db';
        $db = new PDO('sqlite:' . $dbPath);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->exec("PRAGMA foreign_keys = ON");
    }
    return $db;
}