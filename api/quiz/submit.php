<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';

$auth = new AuthMiddleware();
$user = $auth->authenticate();

$data = json_decode(file_get_contents('php://input'), true);
$answers = $data['answers'] ?? [];

if (!is_array($answers) || empty($answers)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Quiz answers are required'
    ]);
    exit;
}

$optionIds = [];

foreach ($answers as $answer) {
    if (!isset($answer['option_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Each answer must include option_id'
        ]);
        exit;
    }

    $optionIds[] = (int)$answer['option_id'];
}

$placeholders = implode(',', array_fill(0, count($optionIds), '?'));

try {
    $stmt = $pdo->prepare("
        SELECT category, score_value
        FROM quiz_options qo
        WHERE id IN ($placeholders)
    ");
    $stmt->execute($optionIds);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'No valid quiz options were submitted'
        ]);
        exit;
    }

    $scores = [];

    foreach ($rows as $row) {
        $category = $row['category'];
        $scores[$category] = ($scores[$category] ?? 0) + (int)$row['score_value'];
    }

    arsort($scores);
    $topCategory = array_key_first($scores);

    $careersStmt = $pdo->prepare("
        SELECT id, name, category, short_description, education_required, time_to_start
        FROM careers
        WHERE category = :category
        ORDER BY name ASC
        LIMIT 6
    ");
    $careersStmt->execute(['category' => $topCategory]);
    $recommendedCareers = $careersStmt->fetchAll(PDO::FETCH_ASSOC);

    $resultStmt = $pdo->prepare("
        INSERT INTO quiz_results (user_id, top_category, scores_json)
        VALUES (:user_id, :top_category, :scores_json)
    ");
    $resultStmt->execute([
        'user_id' => $user->sub,
        'top_category' => $topCategory,
        'scores_json' => json_encode($scores)
    ]);

    echo json_encode([
        'success' => true,
        'top_category' => $topCategory,
        'scores' => $scores,
        'recommended_careers' => $recommendedCareers
    ], JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit quiz',
        'error' => $e->getMessage()
    ]);
}
