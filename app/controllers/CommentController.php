<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Comment;
use App\Core\Database;
use App\Validation\CommentValidation;

class CommentController extends Controller
{
    private $model;
    private $validator;

    public function __construct()
    {
        $db = new Database();
        $this->model = new Comment($db->getConnection());
        $this->validator = new CommentValidation();
    }

    public function create()
    {
        header('Content-Type: application/json; charset=utf-8');

        $postId = $_POST['postId'];
        $content = $_POST['content'];

        $error = $this->validator->validateComment($postId, $content);

        if ($error) {
            echo json_encode(['status' => "error", "message" => $error]);
            exit();
        }

        if (!$this->model->create($postId, $_SESSION['user_id'], $content)) {
            echo json_encode(['status' => "error", "message" => "Could not insert the comment."]);
            exit();
        }
        echo json_encode(['status' => "success", "message" => "Comment created."]);
        exit();
    }
}