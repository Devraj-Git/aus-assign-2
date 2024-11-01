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
                    $this->error_400('Record with car number'. $id .'Not Found !!');
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
                    $this->error_400("The Track type must be either 'race' or 'street'. ");
                }
                $track->name = $_POST['name'];
                $track->laps = $_POST['laps'];
                $track->baseLapTime = $_POST['baseLapTime'];
                $track->save();
                redirect(url('track/'.$track->id));
            }       
            else{
                $this->error_400("All fields are required !");
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
            if($id){
                (new Track($id))->delete();
                $this->response("Record with Track id $id deleted successfully.");
            }
            else{
                $this->error_400("Id field is required !");
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
                    $this->error_400("Record with Driver number $id Not Found !!");
                }
            }
            else{
                $this->error_400("id field is required !\n");
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
                    $this->error_400("Record with Track id $id Not Found !!");
                }
            }       
            else{
                $this->error_400("Track Id fields is required !");
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
                $this->error_400("Id field is required !");
            }
        }
    }
}