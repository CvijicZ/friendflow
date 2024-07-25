<?php

namespace App\Migrations;

class CreateFriendsTable extends Migration
{
    public function up()
    {
        $this->dropIfExists('friends');

        $sql = 'CREATE TABLE friends (
                id INT AUTO_INCREMENT PRIMARY KEY,
                requestor_id INT NOT NULL,
                receiver_id INT NOT NULL,
                sum_of_user_ids INT UNIQUE NOT NULL,
                friendshipType VARCHAR(100),
                date DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (requestor_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
                )';

        $this->execute($sql, "Database table 'friends' created");

        $triggerSql = 'CREATE TRIGGER before_insert_friends
                       BEFORE INSERT ON friends
                       FOR EACH ROW
                       BEGIN
                          SET NEW.sum_of_user_ids = NEW.requestor_id + NEW.receiver_id;
                       END;';

        $this->execute($triggerSql, "Trigger for friends table created");

        $procedureSql = 'DROP PROCEDURE IF EXISTS getAllFriends;
                        CREATE PROCEDURE getAllFriends(IN userId INT)
                         BEGIN
                            SELECT 
                                CASE 
                                    WHEN requestor_id = userId THEN receiver_id
                                    ELSE requestor_id
                                END AS friend_id
                            FROM 
                                friends
                            WHERE 
                                requestor_id = userId OR receiver_id = userId;
                        END';

        $this->execute($procedureSql, "Stored procedure getAllFriends created");
    }

    public function down()
    {
        $sql = "DROP TABLE IF EXISTS friends";
        $this->execute($sql, "Database table 'posts' dropped");
    }
}
