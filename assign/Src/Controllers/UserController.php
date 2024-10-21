<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Users;

class UserController extends Controller
{
    public function login()
    {
        if(authenticate()) {
            redirect(url(''));
        }
        $this->render('user/login/index');
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? $_POST['username'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $user_describe = isset($_POST['user_describe']) ? $_POST['user_describe'] : '';
            $custom_data = isset($_POST['custom_data']) ? $_POST['custom_data'] : '';
            
            $user = new Users;
            
            // echo $username. $password . $user_describe . $custom_data;
            $existingUser = $user->where('username', $username)->first();
            if ($existingUser) {
                set_message('Username already taken, please choose a different one.', 'danger');
                $this->render('user/register/index');
            }

            $pepper = config('SECRET_PEPPER');
            $salt = bin2hex(random_bytes(16));
            $saltedPassword = $salt . $password . $pepper;
            $hashedPassword = password_hash($saltedPassword, PASSWORD_BCRYPT);

            $user->username = $username;
            $user->password = $hashedPassword;
            $user->salt = $salt;
            $user->user_describe = $user_describe;
            $user->custom_data = encrypt($custom_data);
            $user->save();
            redirect(url('logout'));

        }
        else{
            $this->render('user/register/index');
        }

    }

    public function _404() {
        $data = 'Upps! Page not found!';
        $code = 404;
        http_response_code($code);
        view('404',compact("data","code"));
    }

    public function _403() {
        $data = 'Upps! Forbidden access!';
        $code = 403;
        http_response_code($code);
        view('404',compact("data","code"));
    }
}