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

    public function show($id)
    {
        $sql = "SELECT id, name, surname, email, birthday FROM users WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public function getEmailById($id)
    {
        $sql = "SELECT email FROM users WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
    public function update($name, $surname, $email, $birthDate, $password = null)
    {
        $userId = $_SESSION['user_id'];
        $query = "UPDATE users SET name = :name, surname = :surname, email = :email, birthday = :birthday";

        if ($password !== null) {
            $query .= ", password = :password";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':birthday', $birthDate, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        if ($password !== null) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    public function emailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
