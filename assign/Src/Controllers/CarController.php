<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Skills;

class CarController extends Controller
{
    public function __construct() {
        $this->checkAuth();
    }

    public function index($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $car = (new Car)->getcarWithSuitability($id);
                if($car){
                    $this->response($car);
                }
                else{
                    $this->error_400('Record with car number'. $id .'Not Found !!');
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
                    $this->error_400('Invalid suitability data provided.');
                }
                $race = (int)$suitabilityData['race'];
                $street = (int)$suitabilityData['street'];
                if (($race + $street) !== 100) {
                    $this->error_400('The sum of race and street must be 100.');
                }
                if (isset($_POST['driver'])){
                    $get_drivers_in_car = (new Car)->where('driver',$_POST['driver'])->first();
                    if ($get_drivers_in_car) {
                        $this->error_400("This Driver is already assigned to the car ". $get_drivers_in_car->id);
                    }
                    else{
                        $find_driver = (new Driver)->where('number',$_POST['driver'])->first();
                        if($find_driver){
                            $car->driver = $_POST['driver'];
                        }
                        else{
                            $this->error_400("The Driver not found for the number" .$_POST['driver'] ." !!");
                        }
                    }
                }
                if ($_POST['reliability']>=0 && $_POST['reliability'] <= 100){
                    $car->reliability = $_POST['reliability'];
                }
                else{
                    $this->error_400("The car reliability must be between 0 and 100.");
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
                $this->error_400("Suitability and Reliability field are required !");
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
                            $this->error_400("The car reliability must be between 0 and 100.");
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
                            $this->error_400("The Driver not found for the number" .$_PUT['driver'] ." !!");
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
                            $this->error_400("Invalid suitability data provided.");
                        }
                        $race = (int)$suitabilityData['race'];
                        $street = (int)$suitabilityData['street'];
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
                    $skills->save();
                    http_response_code(200);
                    redirect(url('car/'.$id));
                }
                else{
                    $this->error_400('Driver Not Found !!');
                }
            }
            else{
                $this->error_400('number field is required !!');
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
            if($id){
                (new Car($id))->delete();
                (new Skills)->delete(["car_number"=>$id]);
                $this->response("Record with Car number $id deleted successfully.");

            }
            else{
                $this->error_404("Id field is required !\n");
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
                    $this->error_404("Record with Driver number $id Not Found !!");
                }
            }
            else{
                $this->error_400("id field is required !\n");
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'PUT'){
            if($id){
                $car = (new Car)->where('id',$id)->first();
                if($car){
                    $inputData = file_get_contents("php://input");
                    $_PUT = json_decode($inputData, true);
                    if (isset($_PUT['driver'])){
                        $get_drivers_in_car = (new Car)->where('driver',$_PUT['driver'])->Where('id','!=',$id)->first();
                        if ($get_drivers_in_car) {
                            $this->error_400("This Driver is already assigned to another car.");
                        }
                        else{
                            $find_driver = (new Driver)->where('number',$_PUT['driver'])->first();
                            if($find_driver){
                                $car->driver = $_PUT['driver'];
                            }
                            else{
                                $this->error_400("The Driver not found for the number " .$_PUT['driver'] ." !!");
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
                    $this->error_400('Car Not Found !!');
                }
            }
            else{
                $this->error_400('number field is required !');
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
            if($id){
                $driver_del = new Car($id);
                $driver_del->driver = null;
                $driver_del->save();
                $this->response("Record of driver with Car number $id deleted successfully.");
            }
            else{
                $this->error_400('Id field is required !!');
            }
        }
    }

    public function lap($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $car = (new Car)->where('id',$id)->first();
                if($car){
                    if (isset($_GET['baseLapTime']) && isset($_GET['trackType'])){
                        $baseLapTime = $_GET['baseLapTime'];
                        $trackType = $_GET['trackType'];
                        $crashes = false;
                        $randomness = number_format(rand(0, 5000) / 1000, 3);

                        if($trackType == 'street'){
                            $rand_no = rand(0, $car->reliability) + 10;
                        }
                        elseif($trackType == 'race'){
                            $rand_no = rand(0, $car->reliability) + 5;
                        }
                        if($rand_no > $car->reliability){
                            $lap_time = 0;
                            $crashes = true;
                        }
                        else{
                            $suitability = $car->skills()->first()->$trackType;
                            $driver_skill = $car->drivers()->first()->$trackType;
                            if(!$driver_skill){
                                $this->error_418(" I'm a Teapot.");                                
                            }
                            $reliability_factor = 100 - (int)$car->reliability;
                            $speed = ((int)$suitability + (int)$driver_skill + $reliability_factor)/3;
                            $lap_time = number_format($baseLapTime + 10 * $speed/100,1);
                        }
                        $result = [
                            'time'=>$lap_time,
                            'randomness'=>$randomness,
                            "crashed"=> $crashes ? 'true' : 'false'
                        ];
                        $this->response($result);
                    }
                    else{
                        $this->error_400('baseLapTime and trackType fields are required. !!');
                    }
                }
                else{
                    $this->error_400('Car Not Found !!');
                }
            }
            else{
                $this->error_400('Id field is required.');
            }
        }
    }
}