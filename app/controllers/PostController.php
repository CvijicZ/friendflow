<?php

namespace App\Controllers;

use App\Validation\PostValidation;
use App\Core\Flash;
use App\Core\Database;
use App\Models\Post;
use App\Core\Controller;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\CSRFMiddleware;
use Exception;

class PostController extends Controller
{
    private $model;
    private $validator;

    public function __construct()
    {
        $db = new Database();
        $this->model = new Post($db->getConnection());
        $this->validator = new PostValidation($db->getConnection());
    }

    public function create()
    {
        $content = $_POST['post_text'];
        $file = [];
        $filename = '';

        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['post_image'];
            $uploadDir = 'app/storage/images/post_images/';
            $filename = uniqid('post_image_') . '_' . basename($file['name']);
            $targetPath = $uploadDir . $filename;

            // Move uploaded file to storage folder
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                Flash::set('error', 'Failed to move uploaded file.');
                header("Location: /friendflow");
                exit();
            }
        }
        $errors = $this->validator->validatePost($content, $file);
        if ($errors) {
            foreach ($errors as $error) {
                Flash::set('error', $error);
            }
        } else {
            if ($this->model->store($content, $filename)) {
                Flash::set('success', "Post successfully created.");
            } else {
                Flash::set('error', "Failed to create post.");
            }
        }
        header("Location: /friendflow");
        exit();
    }

    public function delete($id, $_token)
    {
        if (CSRFMiddleware::compare($_token)) {
            header('Content-Type: application/json; charset=utf-8');
            if ($this->validator->usersPost($id)) {

                $post = $this->model->show($id);

                if ($post && $this->model->destroy($id)) {
                    $imagePath = "app/storage/images/post_images/" . $post['image_name'];
                    if (!empty($post['image_name']) && is_file($imagePath)) {
                        unlink($imagePath);
                    }
                    echo json_encode(['status' => "success", "message" => "Post deleted."]);
                    exit();
                }
                echo json_encode(['status' => "error", 'message' => "Could not find post to delete"]);
                exit();
            }
            echo json_encode(['status' => "error", 'message' => "Insufficient permission"]);
            exit();
        }
        echo json_encode(['status' => "error", 'message' => "Invalid CSRF."]);
        exit();
    }

    public function update($id, $newContent, $_token)
    {
        header('Content-Type: application/json; charset=utf-8');
        if (CSRFMiddleware::compare($_token)) {

            if ($this->validator->usersPost($id)) {
                if ($this->validator->validateContent($newContent)) {
                    if ($this->model->update($id, $newContent)) {
                        echo json_encode(['status' => "success", 'message' => "Post updated."]);
                        exit();
                    }
                    echo json_encode(['status' => "error", 'message' => "Could not update post."]);
                    exit();
                }
                echo json_encode(['status' => "error", 'message' => "Invalid content.", 'debug' => $this->validator->validateContent($newContent)]);
                exit();
            }
            echo json_encode(['status' => "error", 'message' => "Insufficient permission"]);
            exit();
        }
        echo json_encode(['status' => "error", 'message' => "Invalid CSRF"]);
        exit();
    }

    public function getComments()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $postId = $_POST['postId'];

            $comments = $this->model->getComments($postId);

            echo json_encode(['status' => "success", 'comments' => $this->sanitizeArray($comments)]);
            exit();
        } catch (Exception $e) {
            echo json_encode(['status' => "error", 'message' => "Unexpected error"]);
            exit();
        }
    }

    public function getPosts()
    {
        header('Content-Type: application/json; charset=utf-8');

        $userId = AuthMiddleware::getUserId();
        $limit=$_POST['limit'];
        $offset=$_POST['offset'];

        $posts = $this->model->getPostsFromFriends($userId, $limit, $offset);

        if ($posts) {
            echo json_encode(['status' => "success", 'posts' => $this->sanitizeArray($posts)]);
            exit();
        }

        echo json_encode(['status' => "error", 'message' => "No posts to show"]);
        exit();
    }
}
