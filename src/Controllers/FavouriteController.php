<?php

require_once __DIR__ . '/../Repositories/FavouriteRepository.php';
require_once __DIR__ . '/../Helpers/Response.php';

class FavouriteController
{
    private FavouriteRepository $repo;

    public function __construct()
    {
        $this->repo = new FavouriteRepository();
    }

    public function add($userId): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $careerId = $input['career_id'] ?? null;

        if (!$careerId) {
            Response::json(['success' => false, 'message' => 'career_id required'], 400);
        }

        $this->repo->addFavourite($userId, $careerId);

        Response::json([
            'success' => true,
            'message' => 'Career added to favourites'
        ]);
    }

    public function list($userId): void
    {
        $favourites = $this->repo->getUserFavourites($userId);

        Response::json([
            'success' => true,
            'data' => $favourites
        ]);
    }
}