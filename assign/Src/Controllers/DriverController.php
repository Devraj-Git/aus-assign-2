<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Driver;

class DriverController extends Controller
{
    public function index()
    {
        header('Content-Type: application/json');
        $driver = (new Driver)->get();
        echo json_encode($driver);
    }
}