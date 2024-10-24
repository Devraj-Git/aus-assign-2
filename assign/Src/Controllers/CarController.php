<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Skills;

class CarController extends Controller
{
    public function index($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $car = (new Car)->getcarWithSuitability($id);
                if($car){
                    $this->response($car);
                }
                else{
                    http_response_code(404);
                    echo "Record with car number $id Not Found !!";
                }
            }
            else{
                $car = (new Car)->getcarWithSuitability();
                $this->response($car);
            
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['suitability']) && isset($_POST['reliability'])){
                $car = new Car;
                $suitabilityData = json_decode($_POST['suitability'], true);
                if (!isset($suitabilityData['race']) || !isset($suitabilityData['street'])) {
                    http_response_code(400);
                    echo "Invalid suitability data provided.";
                    return;
                }
                $race = (int)$suitabilityData['race'];
                $street = (int)$suitabilityData['street'];
                if (($race + $street) !== 100) {
                    http_response_code(400);
                    echo "The sum of race and street must be 100.";
                    return;
                }
                if (isset($_POST['driver'])){
                    $get_drivers_in_car = (new Car)->where('driver',$_POST['driver'])->first();
                    if ($get_drivers_in_car) {
                        http_response_code(400);
                        echo "This Driver is already assigned to the car ". $get_drivers_in_car->id;
                        return;
                    }
                    else{
                        $find_driver = (new Driver)->where('number',$_POST['driver'])->first();
                        if($find_driver){
                            $car->driver = $_POST['driver'];
                        }
                        else{
                            http_response_code(400);
                            echo "The Driver not found for the number" .$_POST['driver'] ." !!";
                            return;
                        }
                    }
                }
                if ($_POST['reliability']>=0 && $_POST['reliability'] <= 100){
                    $car->reliability = $_POST['reliability'];
                }
                else{
                    http_response_code(400);
                    echo "The car reliability must be between 0 and 100.";
                    return;
                }
                $car->save();

                $skills = new Skills;
                $skills->car_number = $car->id;
                $skills->race = $race;
                $skills->street = $street;
                $skills->save();
                http_response_code(200);
                redirect(url('car/'.$car->id));

            }
            else{
                http_response_code(400);
                echo "Suitability and Reliability field are required !\n";
                print_r($_POST);
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'PUT'){
            if($id){
                $car = (new Car)->where('id',$id)->first();
                if($car){
                    $inputData = file_get_contents("php://input");
                    // echo $inputData;
                    $_PUT = json_decode($inputData, true);
                    // print_r($_PUT);
                    if (isset($_PUT['reliability'])){
                        if ($_PUT['reliability']>=0 && $_PUT['reliability'] <= 100){
                            $car->reliability = $_PUT['reliability'];
                        }
                        else{
                            http_response_code(400);
                            echo "The car reliability must be between 0 and 100.";
                            return;
                        }
                    }
                    else{
                        $car->reliability = $car->reliability;
                    }
                    if (isset($_PUT['driver'])){
                        $find_driver = (new Driver)->where('number',$_PUT['driver'])->first();
                        if($find_driver){
                            $car->driver = $_PUT['driver'];
                        }
                        else{
                            http_response_code(400);
                            echo "The Driver not found for the number" .$_PUT['driver'] ." !!";
                            return;
                        }
                    }
                    else{
                        $car->driver = $car->driver;
                    }
                    $car->save();
                    
                    $skills = (new Skills)->where('car_number',$id)->first();
                    $skills->car_number = $skills->car_number;
                    if (isset($_PUT['suitability'])){
                        $suitabilityData = $_PUT['suitability'];
                        if (!isset($suitabilityData['race']) || !isset($suitabilityData['street'])) {
                            echo "Invalid suitability data provided.";
                            return;
                        }
                        $race = (int)$suitabilityData['race'];
                        $street = (int)$suitabilityData['street'];
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
                    $skills->save();
                    http_response_code(200);
                    redirect(url('car/'.$id));
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
                (new Car)->delete($id);
                (new Skills)->delete(["car_number"=>$id]);
                http_response_code(200);
                echo "Record with Car number $id deleted successfully.";
            }
            else{
                http_response_code(400);
                echo "Id field is required !\n";
                // print_r($_GET);
            }
        }
    }

    public function car_driver($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $car = (new Car)->where('id',$id)->first();
                // $car = new Car($id);
                if($car){
                    redirect(url('driver/'.$car->driver));
                }
                else{
                    http_response_code(404);
                    echo "Record with Driver number $id Not Found !!";
                }
            }
            else{
                http_response_code(400);
                echo "id field is required !\n";
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'PUT'){
            if($id){
                $car = (new Car)->where('id',$id)->first();
                if($car){
                    $inputData = file_get_contents("php://input");
                    // echo $inputData;
                    $_PUT = json_decode($inputData, true);
                    // print_r($_PUT);
                    if (isset($_PUT['driver'])){
                        $get_drivers_in_car = (new Car)->where('driver',$_PUT['driver'])->Where('id','!=',$id)->first();
                        if ($get_drivers_in_car) {
                            http_response_code(400);
                            echo "This Driver is already assigned to another car.";
                            // print_r($get_drivers_in_car);
                            return;
                        }
                        else{
                            $find_driver = (new Driver)->where('number',$_PUT['driver'])->first();
                            if($find_driver){
                                $car->driver = $_PUT['driver'];
                            }
                            else{
                                http_response_code(400);
                                echo "The Driver not found for the number " .$_PUT['driver'] ." !!";
                                return;
                            }
                        }
                    }
                    else{
                        $car->driver = $car->driver;
                    }
                    $car->save();
                    http_response_code(200);
                    redirect(url('car/'.$id));
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
                $driver_del = new Car($id);
                $driver_del->driver = null;
                $driver_del->save();
                http_response_code(200);
                echo "Record of driver with Car number $id deleted successfully.";
            }
            else{
                http_response_code(400);
                echo "Id field is required !\n";
                // print_r($_GET);
            }
        }
    }

    public function lap($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
              echo "Do it later !!";
            }
            else{
                http_response_code(400);
                echo "Id field is required !\n";
            }
        }
    }
}