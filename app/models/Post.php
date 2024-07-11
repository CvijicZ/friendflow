<?php

namespace App\Models;

use PDO;
use Exception;
use App\Models\User;

class Post{
    protected $db;
    private $userModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->userModel=new User($this->db);
    }

    public function index(){
        $sql="SELECT * FROM posts ORDER BY created_at DESC";
        $stmt=$this->db->query($sql);
        $posts=$stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($posts as &$post){
            $post['user']=$this->userModel->show($post['user_id']);
        }
  
        return $posts;
    }

    public function show($id){
        $sql="SELECT * FROM posts WHERE id=:id";

        $stmt=$this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function store(string $content, string $image=null){
        $sql="INSERT INTO posts(user_id, content, image_name) VALUES(:user_id, :content, :image_name)";
        $stmt=$this->db->prepare($sql);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':image_name', $image);

        return $stmt->execute();

    }

    public function destroy($id){
        $sql="DELETE FROM posts WHERE id=:id";

        $stmt=$this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

}