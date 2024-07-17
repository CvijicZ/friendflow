<?php
namespace App\Migrations;

class CreateFriendRequestsTable extends Migration
{
    public function up()
    {
        $this->dropIfExists('friend_requests');

        $sql = 'CREATE TABLE friend_requests(
    id int AUTO_INCREMENT PRIMARY KEY NOT NULL,
    requestor_id int NOT NULL,
    receiver_id int NOT NULL,
    sum_of_user_ids INT UNIQUE NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT "pending",
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (requestor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
    )';
        $this->execute($sql, "Database table 'friend_requests' created");
    }

    public function down()
    {
        $sql = "DROP TABLE IF EXISTS friend_requests";
        $this->execute($sql, "Database table 'posts' dropped");
    }
}












