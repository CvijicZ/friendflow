<?php

namespace App\Migrations;

class CreateFriendsTable extends Migration
{
    public function up()
    {
        $this->dropIfExists('friends');

        $sql = 'CREATE TABLE friends (
    requestor_id INT NOT NULL,
    receiver_id INT NOT NULL,
    friendshipType VARCHAR(100),
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (requestor_id, receiver_id),
    FOREIGN KEY (requestor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
    )';
        $this->execute($sql, "Database table 'friends' created");
    }

    public function down()
    {
        $sql = "DROP TABLE IF EXISTS friends";
        $this->execute($sql, "Database table 'posts' dropped");
    }
}
