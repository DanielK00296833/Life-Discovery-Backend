<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';

$auth = new AuthMiddleware();
$user = $auth->authenticate();

try {
    $resultStmt = $pdo->prepare("
        SELECT id, top_category, scores_json, created_at
        FROM quiz_results
        WHERE user_id = :user_id
        ORDER BY id DESC
        LIMIT 1
    ");
    $resultStmt->execute(['user_id' => $user->sub]);
    $result = $resultStmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode([
            'success' => true,
            'result' => null
        ]);
        exit;
    }

    $careersStmt = $pdo->prepare("
        SELECT id, name, category, short_description
        FROM careers
        WHERE category = :category
        ORDER BY name ASC
        LIMIT 3
    ");
    $careersStmt->execute(['category' => $result['top_category']]);

    echo json_encode([
        'success' => true,
        'result' => [
            'id' => (int)$result['id'],
            'top_category' => $result['top_category'],
            'scores' => json_decode($result['scores_json'], true),
            'created_at' => $result['created_at'],
            'recommended_careers' => $careersStmt->fetchAll(PDO::FETCH_ASSOC)
        ]
    ], JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load latest quiz result',
        'error' => $e->getMessage()
    ]);
}
