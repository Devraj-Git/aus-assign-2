<?php

namespace App\System\Core;
use App\Models\Users;

use function PHPSTORM_META\type;

class Controller
{
    public function __construct()
    {
        // if (isset($_SESSION['user_id'])) {
        //     $user = $this->getAuthenticatedUser();
        //     log_access($user->id);
        // }
        log_access();
    }

    protected function render($view, $data = [])
    {
        if(!empty($data)) {
            extract($data);
        }
        // include "Views/$view.php";
        include  BASEPATH . "/src/Views/$view.php";

    }
    
    protected function checkAuth() {
        if(!authenticate()) {
            set_message('please login to continue.', 'info');
      
            redirect(url('login'));
          }
    }

    protected function onlyRoles(...$roles) {
        $user = $this->getAuthenticatedUser();
        $user_type = $user->user_type()->first()->type;
        if (!in_array($user_type, $roles)) {
            set_message('You do not have permission to access this page.', 'error');
            redirect(url('403'));
        }
    }

    protected function getAuthenticatedUser() {
        $user_id = $_SESSION['user_id']; 
        return (new Users())->where('id', $user_id)->first();
    }
}