<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM careers");
    $careers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($careers, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to fetch careers',
        'message' => $e->getMessage()
    ]);
}