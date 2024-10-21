<?php

namespace App\Controllers;

use App\System\Core\Controller;

class ResponseController extends Controller
{
    public function _404() {
        $data = 'Upps! Page not found!';
        $code = 404;
        http_response_code($code);
        // view('404',compact("data","code"));
    }

    public function _403() {
        $data = 'Upps! Forbidden access!';
        $code = 403;
        http_response_code($code);
        // view('404',compact("data","code"));
    }
}