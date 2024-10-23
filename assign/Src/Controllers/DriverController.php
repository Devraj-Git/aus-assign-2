<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Driver;
use App\Models\Skills;

class DriverController extends Controller
{
    public function index($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $driver = (new Driver)->getDriverWithSkills($id);
                $this->response($driver);
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
                    echo "Invalid skill data provided.";
                    return;
                }
                $race = (int)$skillData['race'];
                $street = (int)$skillData['street'];
                if (($race + $street) !== 100) {
                    echo "The sum of race and street must be 100.";
                    return;
                }
                $existingDriver = $driver->where('number', $_POST['number'])->first();
                if ($existingDriver) {
                    echo "Driver with this number already exists.";
                    return;
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
                redirect('driver/'.$driver->number);

            }
            else{
                echo "All field is required !\n";
                print_r($_POST);
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
                            if ($existingDriver->number !=  $_PUT['number']){
                                echo "Driver with this number already exists.";
                                return;
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
                                echo "Invalid skill data provided.";
                                return;
                            }
                            $race = (int)$skillData['race'];
                            $street = (int)$skillData['street'];
                            if (($race + $street) !== 100) {
                                echo "The sum of race and street must be 100.";
                                return;
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
                        echo "At least number field is required !";
                        return;
                    }
                }
                else{
                    echo "Driver Not Found !!";
                    return;
                }
            }
        }
    }
}