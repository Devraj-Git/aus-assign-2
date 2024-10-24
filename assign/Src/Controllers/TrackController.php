<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Skills;
use App\Models\Track;

class TrackController extends Controller
{
    public function index($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $car = (new Track($id));
                if($car){
                    $this->response($car);
                }
                else{
                    http_response_code(404);
                    echo "Record with car number $id Not Found !!";
                }
            }
            else{
                $car = (new Track)->get();
                $this->response($car);
            
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['name']) && isset($_POST['type']) && isset($_POST['laps']) && isset($_POST['baseLapTime'])){
                $track = new Track;
                if ($_POST['type'] == "race" || $_POST['type'] == "street") {
                    $track->type = $_POST['type'];
                }
                else{
                    http_response_code(400);
                    echo "The Track type must be either 'race' or 'street'. ";
                    return;
                }
                $track->name = $_POST['name'];
                $track->laps = $_POST['laps'];
                $track->baseLapTime = $_POST['baseLapTime'];
                $track->save();
                http_response_code(200);
                redirect(url('track/'.$track->id));
            }       
            else{
                http_response_code(400);
                echo "All fields are required !\n-------------------------\n";
                print_r($_POST);
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
            if($id){
                (new Track($id))->delete();
                http_response_code(200);
                echo "Record with Track id $id deleted successfully.";
            }
            else{
                http_response_code(400);
                echo "Id field is required !\n";
                // print_r($_GET);
            }
        }
    }

    public function track_races($id=null)
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
                $driver_del = new Track($id);
                // $driver_del->driver = null;
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

    public function scrape($id=null)
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