<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Database;
use App\Validation\UserValidator;
use App\Core\Flash;
use App\Services\JWTService;

class AuthController extends Controller
{
    protected $db;
    private $jwt;

    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
        $this->jwt=new JWTService();
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

                // Set session user_id
                $_SESSION['user_id'] = $user->id;

                // Generate JWT token
                $token = $this->jwt->generateToken($user->id);

                // Set the JWT token in a cookie
                setcookie('jwtToken', $token, [
                    'expires' => time() + 3600, // Cookie expiration time
                    'path' => '/', // Cookie path
                    'domain' => '', // Cookie domain (leave empty for default)
                    'secure' => false, // Only send cookie over HTTPS
                    'httponly' => false, // Allows JS access, maybe there is a better way
                    'samesite' => 'Lax' // CSRF protection
                ]);

                // Redirect to the homepage or another secure page
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
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();

        header('Location: /friendflow/');
        exit();
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
