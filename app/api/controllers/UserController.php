<?php

namespace App\Controllers\Api;

use App\Controllers\Api\Controller;
use App\Core\Database;
use App\Validation\UserValidator;
use App\Models\User;
use App\Services\JWTService;

class UserController extends Controller
{

    private $db;
    private $jwtService;

    public function __construct()
    {
        $this->db = new Database();
        $this->jwtService=new JWTService();
    }

    public function login(){
        $jsonData = file_get_contents('php://input');

        $data = json_decode($jsonData, true);

        $userModel = new User($this->db->getConnection());
        $user = $userModel->findByEmail($data['email']);
        $userValidator=new UserValidator($userModel);

        $errors=$userValidator->validateLogin($data);

        if(empty($errors)){
            if($user && password_verify($data['password'], $user->password)){

                $token=$this->jwtService->generateToken($user->id);
                $this->successResponse(['token' => $token], "success");
                exit();

            }
            $this->successResponse(['message' => 'Wrong credentials']);
            exit();
        
        }
        $this->errorResponse($errors, 'Invalid login');
        exit();


    }

    public function index()
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->successResponse($data);
        exit();
    }

    public function show($id)
    {
        $sql = "SELECT id, name, surname, email, birthday FROM users WHERE id=:id";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($user) {
            $this->successResponse($user);
            exit();
        }
        $this->errorResponse("User not found", 404);
    }

    public function store()
    {

        $jsonData = file_get_contents('php://input');

        // Decode the JSON data into a PHP associative array
        $data = json_decode($jsonData, true);

        $name = trim($data['name']);
        $surname = trim($data['surname']);
        $email = trim($data['email']);
        $password = trim($data['password']);
        $passwordRepeat = trim($data['password_repeated']);
        $birthYear = trim($data['birth_year']);
        $birthMonth = trim($data['birth_month']);
        $birthDay = trim($data['birth_day']);

        $birthDate = "$birthYear-$birthMonth-$birthDay";

        $validator = new UserValidator(new User($this->db->getConnection()));
        $errors = $validator->validateProfileInfo([
            "name" => $name,
            "surname" => $surname,
            "email" => $email,
            "password" => $password,
            "password_repeated" => $passwordRepeat,
            "birthday" => $birthDate
        ]);

        if (!$validator->hasErrors()) {
            $userModel = new User($this->db->getConnection());
            if ($userModel->create($name, $surname, $email, $birthDate, $password)) {
                $this->successResponse(null, "Registered successfully");
                exit();
            } else {

                $this->errorResponse("Internal server error", 500);
                exit();
            }
        } else {
            $this->errorResponse(json_encode($errors), "Bad request", 400);
            exit();
        }
    }
}
