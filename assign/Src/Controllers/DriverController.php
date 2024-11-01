<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Driver;
use App\Models\Skills;

class DriverController extends Controller
{
    public function __construct() {
        $this->checkAuth();
    }
    public function index($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $driver = (new Driver)->getDriverWithSkills($id);
                if($driver){
                    $this->response($driver);
                }
                else{
                    $this->error_400("Record with Driver number $id Not Found !!");
                }
            }
            else{
                $driver = (new Driver)->getDriverWithSkills();
                $this->response($driver);
            
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['number']) && isset($_POST['shortName']) && isset($_POST['name']) && isset($_POST['skill'])){
                $driver = new Driver;
                $skillData = json_decode($_POST['skill'], true);
                if (!isset($skillData['race']) || !isset($skillData['street'])) {
                    $this->error_400("Invalid skill data provided.");
                }
                $race = (int)$skillData['race'];
                $street = (int)$skillData['street'];
                if (($race + $street) !== 100) {
                    $this->error_400("The sum of race and street must be 100.");
                }
                $existingDriver = $driver->where('number', $_POST['number'])->first();
                if ($existingDriver) {
                    $this->error_400("Driver with this number already exists.");
                }
                $driver->number = $_POST['number'];
                $driver->shortName = $_POST['shortName'];
                $driver->name = $_POST['name'];
                $driver->save();

                $skills = new Skills;
                $skills->driver_number = $driver->number;
                $skills->race = $race;
                $skills->street = $street;
                $skills->save();
                http_response_code(200);
                redirect('driver/'.$driver->number);

            }
            else{
                $this->error_400("All field are required !");
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'PUT'){
            if($id){
                $driver = (new Driver)->where('number',$id)->first();
                if($driver){
                    $inputData = file_get_contents("php://input");
                    // echo $inputData;
                    $_PUT = json_decode($inputData, true);
                    // print_r($_PUT);
                    if (isset($_PUT['number'])){
                        $existingDriver = (new Driver)->where('number', $_PUT['number'])->first();
                        if ($existingDriver) {
                            if ($id !=  $_PUT['number']){
                                $this->error_400("Driver with this number already exists.");
                            }
                        }
                        $driver->number = $_PUT['number'];
                        if (isset($_PUT['shortName'])){
                            $driver->shortName = $_PUT['shortName'];
                        }
                        else{
                            $driver->shortName = $driver->shortName;
                        }
                        if (isset($_PUT['name'])){
                            $driver->name = $_PUT['name'];
                        }
                        else{
                            $driver->name = $driver->name;
                        }

                        $skills = (new Skills)->where('driver_number',$id)->first();
                        $skills->driver_number = $driver->number;
                        if (isset($_PUT['skill'])){
                            $skillData = $_PUT['skill'];
                            if (!isset($skillData['race']) || !isset($skillData['street'])) {
                                $this->error_400("Invalid skill data provided.");
                            }
                            $race = (int)$skillData['race'];
                            $street = (int)$skillData['street'];
                            if (($race + $street) !== 100) {
                                $this->error_400("The sum of race and street must be 100.");
                            }   
                            $skills->race = $race;
                            $skills->street = $street;
                        }
                        else{
                            $skills->race = $skills->race;
                            $skills->street = $skills->street;
                        }
                        $driver->save();
                        $skills->save();
                        $url = config('app_url').'driver/'.$_PUT['number'];
                        header("Location: $url");
                    }
                    else{
                        $this->error_400("At least number field is required !");
                    }
                }
                else{
                    $this->error_400("Driver Not Found !");
                }
            }
            else{
                $this->error_400("Number Field is required !");
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
            if($id){
                (new Driver)->delete(['number' => $id]);
                (new Skills)->delete(["driver_number"=>$id]);
                $this->response("Record with Driver number $id deleted successfully !!");

            }
            else{
                $this->error_400("All field are required !");
            }
        }
    }
}