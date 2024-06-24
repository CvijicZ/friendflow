<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Database;
use App\Core\UserValidator;
use App\Core\Flash;

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
    public function showLoginForm()
    {
        $this->view('auth/login');
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header('Location: /friendflow/');
        exit();
    }
    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $validator = new UserValidator(new User($this->db));
        $errors = $validator->validateLogin([
            "email" => $email,
            "password" => $password
        ]);

        $userModel = new User($this->db);
        $user = $userModel->findByEmail($email);

        if (!$validator->hasErrors()) {
            if ($user && password_verify($password, $user->password)) {
                $_SESSION['user_id'] = $user->id;

                header('Location: /friendflow');
                exit();
            } else {
                Flash::set('error', 'Invalid email or password.');
                header('Location: /friendflow/login');
                exit();
            }
        } else {
            foreach ($errors as $error) {
                Flash::set('error', $error);
            }
            header("Location: /friendflow/login");
            exit();
        }
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
        $errors = $validator->validateProfileInfo([
            "name" => $name,
            "surname" => $surname,
            "email" => $email,
            "password" => $password,
            "password_repeated" => $passwordRepeat,
            "birthday" => $birthDate
        ]);

        if (!$validator->hasErrors()) {
            $userModel = new User($this->db);
            if ($userModel->create($name, $surname, $email, $birthDate, $password)) {
                Flash::set('success', "User successfuly registered.");
                header("Location: /friendflow/login");
                exit();
            } else {
                Flash::set('error', "Something went wrong.");
            }

        } else {
            foreach ($errors as $error) {
                Flash::set('error', $error);
            }
            header("Location: /friendflow/register");
            exit();
        }
    }
}
