<?php

namespace App\Controllers\Api;

use App\Controllers\Api\Controller;
use App\Core\Database;
use App\Validation\UserValidator;
use App\Models\User;
use App\Services\JWTService;
use App\Models\Post;

class PostController extends Controller
{
    private $model;
    private $db;
    private $jwtService;

    public function __construct()
    {
        $this->db = new Database();
        $this->jwtService = new JWTService();
        $this->model = new Post($this->db->getConnection());
    }

    public function getComments($queryParams = [])
    {
        $postId = $queryParams['params']['id'];
        $comments = $this->model->getComments($postId);
        $this->successResponse($comments);
        exit();
    }

    public function index($queryParams = [])
    {
        try {

            $userId = $this->jwtService->extractAndValidateToken();

            if (!isset($queryParams['params']['offset']) || !isset($queryParams['params']['limit'])) {
                $this->errorResponse(null, "Bad request", 404);
                exit();
            }


            $offset = $queryParams['params']['offset'];
            $limit = $queryParams['params']['limit'];




            $posts = $this->model->getPostsFromFriends($userId, $limit, $offset);

            $this->successResponse($posts);
            exit();
        } catch (\Exception $e) {
            $this->errorResponse(null, "Not authorized", 403);
            exit();
        }
    }

    public function create() {}
}
