<?php

namespace App\Controllers;

use App\System\Core\Controller;

class ResponseController extends Controller
{
    public function _404() {
        $data = 'Not found!';
        $code = 404;
        http_response_code($code);
        // view('404',compact("data","code"));
    }

    public function _403() {
        $data = 'Forbidden access!';
        $code = 403;
        http_response_code($code);
        // view('404',compact("data","code"));
    }
}