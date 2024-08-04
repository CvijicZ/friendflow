<?php

namespace App\Core;

require_once 'Template.php';
class Controller
{
    protected function view($view, $data = [])
    {
        Template::render($view, $data);
    }

    public function sanitizeArray($array)
    {
        $escapedData = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $escapedData[$key] = $this->sanitizeArray($value);
            } else {
                $escapedData[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }

        return $escapedData;
    }
}
