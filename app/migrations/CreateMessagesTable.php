<?php

namespace App\Migrations;

class CreateMessagesTable extends Migration
{

    public function up()
    {
        $this->dropIfExists('messages');

        $sql = 'CREATE TABLE messages(
                  id int AUTO_INCREMENT PRIMARY KEY NOT NULL,
                  sender_id int NOT NULL,
                  recipient_id int NOT NULL,
                  message TEXT NOT NULL,
                  status varchar(50) NOT NULL DEFAULT "sent",
                  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                 )';

        $this->execute($sql, "Database table 'messages' created");
    }
    public function down()
    {
        $sql = "DROP TABLE IF EXISTS messages";
        $this->execute($sql, "Database table 'messages' dropped");
    }
}
