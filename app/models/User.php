<?php

namespace App\Models;

use PDO;
use Exception;

class User
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create($name, $surname, $email, $birthday, $password)
    {
        try {
            $sql = "INSERT INTO users(name, surname, email, birthday, password) VALUES(:name, :surname, :email, :birthday, :password)";
            $stmt = $this->db->prepare($sql);

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':surname', $surname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':birthday', $birthday);

            $stmt->execute();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
