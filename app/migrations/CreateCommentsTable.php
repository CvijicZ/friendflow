<?php
namespace App\Migrations;

class CreateCommentsTable extends Migration
{
    public function up()
    {
        $this->dropIfExists('comments');

        $sql = "CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content VARCHAR(500) NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);";
        $this->execute($sql, "Database table 'comments' created");
    }

    public function down()
    {
        $sql = "DROP TABLE IF EXISTS posts";
        $this->execute($sql, "Database table 'comments' dropped");
    }
}