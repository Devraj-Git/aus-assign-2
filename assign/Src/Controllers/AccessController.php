<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Users;
use App\Models\AccessLog;

class AccessController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->onlyRoles('admin','moderator');
    }

    public function index()
    {
        $user_obj = new Users;
        $access_obj = new AccessLog;
        $id_user = $_SESSION['user_id'];
        $user = $user_obj->where('id',$id_user)->first();
        if (!get_session('view'))
            set_session('view','table');
        if(isset($_GET['session_clear']) && !empty($_GET['session_clear']))
            clear_session($_GET['session_clear']);
        $searchSession = get_session('search');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($searchSession)) {
            if (isset($_POST['ipaddress']))
                $ip = $_POST['ipaddress'];
            else
                $ip = $searchSession;
            $access_log = $access_obj->where('ip_address','LIKE',$ip)->orderBy('created_at','DESC');
            set_session('search',$ip);
        }
        else{
            $access_log = $access_obj->orderBy('created_at','DESC');
        }
        $access_log = $access_log->paginate();
        $this->render('access/index', compact('user','access_log'));
    }

    public function changeView()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['changeView']) && !empty($_POST['changeView'])) {
                $view = $_POST['changeView'];
                set_session('view',$view);
            } else {
                set_message('Select an option before submit !','error');
            }
            goback();
        }
    }

}