<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/Middleware/AuthMiddleware.php';

$auth = new AuthMiddleware();
$user = $auth->authenticate();

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // List user's favourites
    try {
        $stmt = $pdo->prepare("
            SELECT c.* FROM careers c
            INNER JOIN user_favourites uf ON c.id = uf.career_id
            WHERE uf.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $user['id']]);
        $favourites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'favourites' => $favourites]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch favourites', 'message' => $e->getMessage()]);
    }
} elseif ($method === 'POST') {
    // Add to favourites
    $data = json_decode(file_get_contents('php://input'), true);
    $career_id = $data['career_id'] ?? null;

    if (!$career_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Career ID required']);
        exit;
    }

    try {
        // Check if already favourited
        $stmt = $pdo->prepare("SELECT id FROM user_favourites WHERE user_id = :user_id AND career_id = :career_id");
        $stmt->execute(['user_id' => $user['id'], 'career_id' => $career_id]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => true, 'message' => 'Already in favourites']);
            exit;
        }

        // Add to favourites
        $stmt = $pdo->prepare("INSERT INTO user_favourites (user_id, career_id) VALUES (:user_id, :career_id)");
        $stmt->execute(['user_id' => $user['id'], 'career_id' => $career_id]);
        echo json_encode(['success' => true, 'message' => 'Added to favourites']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add favourite', 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>