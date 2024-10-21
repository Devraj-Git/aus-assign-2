<?php

namespace App\System\Core;
use App\Models\Users;

use function PHPSTORM_META\type;

class Controller
{
    protected function render($view, $data = [])
    {
        if(!empty($data)) {
            extract($data);
        }
        include  BASEPATH . "/src/Views/$view.php";

    }
}