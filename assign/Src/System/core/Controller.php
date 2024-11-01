<?php

namespace App\System\Core;
use App\Models\Users;

use function PHPSTORM_META\type;

class Controller
{
    protected function checkAuth() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return;
        }
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $api_key = str_replace('Bearer ', '', $headers['Authorization']);
            if ($api_key !== config('api_key')) {
                header("HTTP/1.1 401 Unauthorized");
                $response = [
                    "code" => 401,
                    "result" => "Invalid API Key"
                ];
                echo json_encode($response, JSON_UNESCAPED_SLASHES);
                exit;
            }
        } else {
            header("HTTP/1.1 403 Forbidden");
            $response = [
                "code" => 403,
                "result" => "API key required"
            ];
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
            exit;
        }
    }
    
    protected function response($data)
    {
        header('Content-Type: application/json');
        http_response_code(200);
        $response = [
            "code" => 200,
            "result" => $data
        ];
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return;
    }

    protected function error_400($data)
    {
        header('Content-Type: application/json');
        http_response_code(400);
        $response = [
            "code" => 400,
            "result" => $data
        ];
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return;
    }

    protected function error_404($data)
    {
        header('Content-Type: application/json');
        http_response_code(404);
        $response = [
            "code" => 404,
            "result" => $data
        ];
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return;
    }

    protected function error_418($data)
    {
        header('Content-Type: application/json');
        http_response_code(418);
        $response = [
            "code" => 418,
            "result" => $data
        ];
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return;
    }

}