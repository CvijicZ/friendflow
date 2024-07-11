<?php

namespace App\Controllers;

use App\Validation\PostValidation;
use App\Core\Flash;
use App\Core\Database;
use App\Models\Post;
use App\Core\Controller;

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

    public function delete($id)
    {
        
        header('Content-Type: application/json; charset=utf-8');
        if ($this->validator->usersPost($id)) {

            if ($this->model->destroy($id)) {
                echo json_encode(['status' => "success", "message" => "Post deleted."]);
                exit();
            }
            echo json_encode(['status' => "error", 'message' => "Could not find post to delete"]);
            exit();
        }
        echo json_encode(['status' => "error", 'message' => "Insufficient permission"]);
        exit();
    }
}