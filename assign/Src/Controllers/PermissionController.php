<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Users;
use App\Models\UsersType;

class PermissionController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->onlyRoles('admin');
    }

    public function index()
    {
        $user_obj = new Users;
        $usertype_obj = new UsersType;
        $id_user = $_SESSION['user_id'];
        $user = $user_obj->where('id',$id_user)->first();
        $all_users = $user_obj->orderBy('role', 'DESC')->get();
        $users_type = $usertype_obj->get();
        $this->render('permission/index', compact('user', 'all_users', 'users_type'));
    }

    public function roleUpdate(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $users = new Users;
            print_r($_POST);
            foreach ($_POST as $key => $value) {
                if (preg_match('/^user_type_(\d+)$/', $key, $matches)) {
                    $user_id = $matches[1];
                    $all_user = $users->where('id',$user_id)->first();
                    if ($all_user && $all_user->user_type()->first()->type != "admin") {
                        $all_user->role = $value;
                        $all_user->save();
                    }
                }
            }
        }
        goback();
    }
}