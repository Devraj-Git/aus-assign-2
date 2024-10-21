<?php

namespace App\Controllers;

use App\Models\Users;
use App\System\Core\Controller;

class LoginController extends Controller {

    public function __construct() {
        parent::__construct();
        if(authenticate()) {
            redirect(url(''));
        }
    }

    public function index() {
        view('user/login/index');
    }

    public function check() {
        $user = new Users;

        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        $check = $user->where('username', $username)->first();

        $pepper = config('SECRET_PEPPER');        
        // $saltedPassword = $check->salt . $password . $pepper;

        if($check && password_verify($check->salt . $password . $pepper, $check->password)) {
            if($check->status == '1') {
                $_SESSION['user_id'] = $check->id;

                if(!empty($_POST['remember']) && $_POST['remember'] == 'yes') {
                    setcookie("User_Log", $check->id, time()+30*24*60*60, '/');
                }

                redirect(url(''));
            } else {
                set_message('Your account is inactive. Please contact site admin.', 'danger');
            redirect(url('login'));

            }
        } else {
            log_access('Failed Login attempt.');
            set_message('Invalid email and/or password.', 'danger');
            redirect(url('login'));
            
        }
    }

}

?>