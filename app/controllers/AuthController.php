<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Database;
use App\Core\UserValidator;

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
        $passwordRepeat = trim($_POST['password_repeated']);
        $birthYear = trim($_POST['birth_year']);
        $birthMonth = trim($_POST['birth_month']);
        $birthDay = trim($_POST['birth_day']);

        $birthDate = "$birthYear-$birthMonth-$birthDay";

        $validator = new UserValidator(new User($this->db));
        $errors = $validator->validateRegistration([
            "name" => $name,
            "surname" => $surname,
            "email" => $email,
            "password" => $password,
            "password_repeated" => $passwordRepeat,
            "birthday" => $birthDate
        ]);

        // TODO: create FLASH session messages for error handling
        if (!$validator->hasErrors()) {
            $userModel = new User($this->db);
            if ($userModel->create($name, $surname, $email, $birthDate, $password)) {
                echo "User registered successfully!";
            } else {
                echo "Error registering user.";
            }

        } else {
            var_dump($errors);
        }
    }
}
