<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing career slug']);
    exit;
}

$slug = $_GET['slug'];

try {
    $stmt = $pdo->prepare("SELECT * FROM careers WHERE slug = :slug LIMIT 1");
    $stmt->execute(['slug' => $slug]);
    $career = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$career) {
        http_response_code(404);
        echo json_encode(['error' => 'Career not found']);
        exit;
    }

    echo json_encode($career, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to fetch career',
        'message' => $e->getMessage()
    ]);
}