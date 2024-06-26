<?php

namespace App\Validation;

use App\Models\User;

class UserValidator
{
    private $errors = [];
    private $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function validateLogin(array $userInputs): array
    {
        $this->validateEmail($userInputs['email'], true);
        $this->validatePassword($userInputs['password']);

        return $this->errors;
    }

    public function validateProfileInfo(array $userInputs, bool $isProfileUpdate = false): array
    {
        $this->alphaOnly("name", $userInputs['name']);
        $this->alphaOnly("surname", $userInputs['surname']);
        $this->validateBirthDate($userInputs['birthday']);

        if ($isProfileUpdate) {

            $this->validateEmail($userInputs['email'], true, true);

            if (!empty($userInputs['password'])) {
                $this->validatePassword($userInputs['password']);
                $this->passwordMatch($userInputs['password'], $userInputs['password_repeated']);
            }

        } else {
            $this->validatePassword($userInputs['password']);
            $this->passwordMatch($userInputs['password'], $userInputs['password_repeated']);
            $this->validateEmail($userInputs['email']);

        }

        return $this->errors;
    }

    function validateBirthDate($birthDate)
    {

        $components = explode('-', $birthDate);

        if (count($components) !== 3) {
            $this->errors['birthdate'] = 'Incorrect birthdate format.';
            return;
        }

        [$birthYear, $birthMonth, $birthDay] = $components;

        if ($birthYear < 1900 || $birthMonth < 1 || $birthMonth > 12) {
            $this->errors['birthdate'] = 'Invalid date.';
            return;
        }

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $birthMonth, $birthYear);
        if ($birthDay < 1 || $birthDay > $daysInMonth) {
            $this->errors['birthdate'] = 'Invalid day for given month.';
        }
    }

    public function alphaOnly($keyName, $string, $allowedLength = 20)
    {
        if (empty($string) || strlen($string) > $allowedLength || !preg_match('/^[\p{L}]+$/u', $string)) {
            $this->errors[$keyName] = 'Only letters are allowed, max length is:' . $allowedLength;
        }
    }
    public function validateEmail($email, $allowTakenEmail = false, $isProfileUpdate = false)
    {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Incorrect email format.';
            return;
        }

        if (!$allowTakenEmail) {
            if ($this->userModel->emailExists($email)) {
                $this->errors['email'] = 'Email is already taken.';
            }
        } elseif ($isProfileUpdate) {
            // For profile updates, check if the email is different from current email and taken
            $currentEmail = $this->userModel->getEmailById($_SESSION['user_id']);
            if ($email !== $currentEmail && $this->userModel->emailExists($email)) {
                $this->errors['email'] = 'Email is already taken.';
            }
        }
    }
    public function validatePassword($password)
    {
        if (empty($password)) {
            $this->errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 6 || strlen($password) > 30) {
            $this->errors['password'] = 'Password must be between 6 and 30 characters long.';
        }
    }

    public function passwordMatch($password, $passwordRepeated)
    {
        if ($password !== $passwordRepeated) {
            $this->errors['password_repeated'] = 'Passwords does not match.';
        }
    }
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
