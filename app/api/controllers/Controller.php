<?php

namespace App\Controllers\Api;

class Controller
{
    protected function response($data, $status = 200, $message = '')
    {
        http_response_code($status);

        header('Content-Type: application/json');
        // header("Access-Control-Allow-Origin: *");
        // header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
        // header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        echo json_encode($response);
        exit;
    }

    public function successResponse($data, $message = 'Success')
    {
        $this->response($data, 200, $message);
    }

    public function errorResponse($data=null, $message, $status = 400)
    {
        $this->response($data, $status, $message);
    }
}
