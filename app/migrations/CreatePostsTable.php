<?php
namespace App\Migrations;
class CreatePostsTable extends Migration
{
    public function up()
    {
        $this->dropIfExists('posts');

        $sql = "CREATE TABLE posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            content TEXT NOT NULL,
            image_name VARCHAR(600) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        $this->execute($sql, "Database table 'posts' created");
    }

    public function down()
    {
        $sql = "DROP TABLE IF EXISTS posts";
        $this->execute($sql, "Database table 'posts' dropped");
    }
}