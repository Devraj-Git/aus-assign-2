<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Race;
use App\Models\Track;

class TrackController extends Controller
{
    public function __construct() {
        $this->checkAuth();
    }
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
                $race = (new Race)->where('track',$id)->first();
                if($race){
                    $this->response($race);
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
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($id)){
                $Track = (new Track)->where('id',$id)->first();
                if($Track){
                    $race = new Race;
                    $race->track = $id;
                    $race->save();
                    http_response_code(200);
                    redirect(url('race/'.$race->id));
                }
                else{
                    http_response_code(400);
                    echo "Record with Track id $id Not Found !!";
                    print_r($_POST);
                }
            }       
            else{
                http_response_code(400);
                echo "Track Id fields is required !\n-------------------------\n";
                print_r($_POST);
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