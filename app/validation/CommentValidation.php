<?php
namespace App\Validation;

use App\Models\Post;
use App\Core\Database;

class CommentValidation
{
    private $error = "";
    private $postModel;

    public function __construct()
    {
        $db = new Database();
        $this->postModel = new Post($db->getConnection());
    }

    public function validateComment($postId, $content)
    {
        if (empty($content) || strlen($content) > 500) {
            $this->error = "Invalid length";
        }

        $post = $this->postModel->show($postId);
        if (empty($post)) {
            $this->error = "Post not found";
        } // TODO: add validation that will allow only friends to comment on the post's

        return $this->error;
    }
}