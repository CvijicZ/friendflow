<?php

namespace App\Validation;

class PostValidation
{
    private $errors = [];

    public function validatePost(string $content, array $image): array
    {
        $this->validateContent($content);

        if (!empty($image)) {
            $this->validateImage($image);

        }

        return $this->errors;
    }
    private function validateImage($file)
    {

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            $this->errors['image'] = "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
        }

        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        if ($file['size'] > $maxFileSize) {
            $this->errors['image'] = "File size exceeds the maximum allowed size (5 MB).";
        }
    }

    private function validateContent($content)
    {
        if (empty($content) || strlen($content) > 2000) {
            $this->errors['content'] = 'Incorrect content length.';
        }

    }
}