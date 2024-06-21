<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Database;

class AuthController extends Controller
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
    }

    public function showRegisterForm()
    {
        $this->view('auth/register');
    }

    public function register()
    {
        $name = trim($_POST['name']);
        $surname = trim($_POST['surname']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $birthYear = trim($_POST['birth_year']);
        $birthMonth = trim($_POST['birth_month']);
        $birthDay = trim($_POST['birth_day']);

        $birthDate = "$birthYear-$birthMonth-$birthDay";
        $userModel = new User($this->db);

        if ($userModel->create($name, $surname, $email, $birthDate, $password)) {
            echo "User registered successfully!";
        } else {
            echo "Error registering user.";
        }
    }
}
