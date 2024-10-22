<?php

namespace App\System\Core;
use App\Models\Users;

use function PHPSTORM_META\type;

class Controller
{
    protected function response($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}