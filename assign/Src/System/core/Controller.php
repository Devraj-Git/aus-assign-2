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
        echo json_encode($data);
    }
}