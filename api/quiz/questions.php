<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';

try {
    $questionsStmt = $pdo->query("
        SELECT id, question_text
        FROM quiz_questions
        ORDER BY RAND()
        LIMIT 10
    ");
    $questions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($questions)) {
        echo json_encode([
            'success' => true,
            'questions' => []
        ]);
        exit;
    }

    $questionIds = array_column($questions, 'id');
    $placeholders = implode(',', array_fill(0, count($questionIds), '?'));

    $optionsStmt = $pdo->prepare("
        SELECT id, question_id, option_text
        FROM quiz_options
        WHERE question_id IN ($placeholders)
        ORDER BY RAND()
    ");
    $optionsStmt->execute($questionIds);
    $options = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);

    $optionsByQuestion = [];

    foreach ($options as $option) {
        $optionsByQuestion[$option['question_id']][] = [
            'id' => (int)$option['id'],
            'option_text' => $option['option_text']
        ];
    }

    foreach ($questions as &$question) {
        $question['id'] = (int)$question['id'];
        $question['options'] = $optionsByQuestion[$question['id']] ?? [];
    }

    echo json_encode([
        'success' => true,
        'questions' => $questions
    ], JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load quiz questions',
        'error' => $e->getMessage()
    ]);
}
