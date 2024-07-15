<?php

namespace App\Migrations;

class CreateUsersTable extends Migration{
    
        public function up()
        {
            $this->dropIfExists('users');
    
            $sql = "CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                surname VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                birthday DATE NOT NULL,
                password VARCHAR(600) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->execute($sql, "Database table 'users' created");
        }
    
        public function down()
        {
            $sql = "DROP TABLE IF EXISTS users";
            $this->execute($sql, "Database table 'users' dropped");
        }

}