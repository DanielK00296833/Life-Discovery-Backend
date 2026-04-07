<?php

require_once __DIR__ . '/../../config/db.php';

class FavouriteRepository
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function addFavourite(int $userId, int $careerId): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO user_favourites (user_id, career_id)
            VALUES (:user_id, :career_id)
        ");

        return $stmt->execute([
            'user_id' => $userId,
            'career_id' => $careerId
        ]);
    }

    public function getUserFavourites(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT c.* FROM user_favourites uf
            JOIN careers c ON c.id = uf.career_id
            WHERE uf.user_id = :user_id
        ");

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}