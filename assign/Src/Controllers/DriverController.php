<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Driver;

class DriverController extends Controller
{
    public function index($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                echo $id;
            }
            else{
                $driver = (new Driver)->get();
                $this->response($driver);
            
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            echo "Post";
        }
        if($_SERVER['REQUEST_METHOD'] === 'PUT'){
            echo "PUT";
            if($id){
                echo $id;
            }
        }
    }
}