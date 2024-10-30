<?php

namespace App\System\Core;
use App\Models\Users;

use function PHPSTORM_META\type;

class Controller
{
    protected function response($data)
    {
        header('Content-Type: application/json');
        http_response_code(200);
        $response = [
            "code" => 200,
            "result" => $data
        ];
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
    }
}