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
                if($driver){
                    $this->response($driver);
                }
                else{
                    http_response_code(404);
                    echo "Record with Driver number $id Not Found !!";
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
                    http_response_code(400);
                    echo "Invalid skill data provided.";
                    return;
                }
                $race = (int)$skillData['race'];
                $street = (int)$skillData['street'];
                if (($race + $street) !== 100) {
                    http_response_code(400);
                    echo "The sum of race and street must be 100.";
                    return;
                }
                $existingDriver = $driver->where('number', $_POST['number'])->first();
                if ($existingDriver) {
                    http_response_code(400);
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
                http_response_code(200);
                redirect('driver/'.$driver->number);

            }
            else{
                http_response_code(400);
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
                            if ($id !=  $_PUT['number']){
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
                        http_response_code(200);
                        header("Location: $url");
                    }
                    else{
                        http_response_code(400);
                        echo "At least number field is required !";
                        return;
                    }
                }
                else{
                    http_response_code(400);
                    echo "Driver Not Found !!";
                    return;
                }
            }
            else{
                http_response_code(400);
                echo "number is required !\n";
                print_r($_POST);
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
            if($id){
                (new Driver)->delete(['number' => $id]);
                (new Skills)->delete(["driver_number"=>$id]);
                http_response_code(200);
                echo "Record with Driver number $id deleted successfully.";
            }
            else{
                http_response_code(400);
                echo "All field is required !\n";
                print_r($_POST);
            }
        }
    }
}