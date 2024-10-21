<?php

namespace App\Controllers;

use App\System\Core\Controller;

class LogoutController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->checkAuth();
    }

    public function index() {
        if(authenticate()) {
            set_message('You have been logged out.','info');
        }
        else{
            set_message('Please login to continue.','success');
        }

        session_unset();

        if(!empty($_COOKIE['User_Log'])) {
            setcookie('User_Log', '', time() - 120, '/');
        }

        redirect(url('login'));
    }
}