<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Skills;
use App\Models\Race;

class RaceController extends Controller
{
    public function index($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $Race = (new Race)->where('id',$id)->first();
                if($Race){
                    if (isset($Race->entrants) && is_string($Race->entrants)) {
                        $Race->entrants = json_decode($Race->entrants);
                    }
                }
                else{
                    http_response_code(404);
                    echo "Record with Race number $id Not Found !!";
                    return;
                }
            }
            else{
                $Race = (new Race)->get();
                foreach ($Race as &$rac) {
                    if (isset($rac->entrants) && is_string($rac->entrants)) {
                        $rac->entrants = json_decode($rac->entrants);
                    }
                }
            }
            $this->response($Race);
        }
    }

    public function race_entrant($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $Race = (new Race)->where('id',$id)->select('entrants')->first();
                if($Race){
                    $this->response(json_decode($Race->entrants, true));
                }
                else{
                    http_response_code(404);
                    echo "Record with Race id $id Not Found !!";
                }
            }
            else{
                http_response_code(400);
                echo "id field is required !\n";
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['Car_URI'])){
                $carUri = $_POST['Car_URI'];
                if (preg_match('/car\/(\d+)$/', $carUri, $matches)) {
                    $carNumber = $matches[1];
                    if(!(new Car)->where('id',$carNumber)->first()){
                        http_response_code(400);
                        echo "Record with Car id $carNumber Not Found !!";
                        return;
                    }
                } else {
                    http_response_code(400);
                    echo "The Car_URI should end with 'car/{number}' pattern.\n";
                    return;
                }
                if($id){
                    $Race = (new Race)->where('id',$id)->first();
                    if($Race){
                        $currentEntrants = !empty($Race->entrants) ? json_decode($Race->entrants, true) : [];
                        if (!in_array($carUri, $currentEntrants)) {
                            $currentEntrants[] = $carUri;
                        } else {
                            http_response_code(404);
                            echo "Record with Entrants uri $carUri in Race number $id is already exists !!";
                            return;
                        }
                        $Race->entrants = json_encode(array_values($currentEntrants));
                        $Race->save();
                        http_response_code(200);
                        redirect(url('race/'.$Race->id));

                    }
                    else{
                        http_response_code(404);
                        echo "Record with Race id $id Not Found !!";
                        return;
                    }               
                }
                else{
                    http_response_code(400);
                    echo "id field is missing in url !\n";
                    return;
                }
            }
            else{
                http_response_code(400);
                echo "'Car_URI' field is required !\n";
                print_r($_POST);
                return;
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
            $input = json_decode(file_get_contents('php://input'), true);
            if (isset($input['Car_URI'])){
                $carUri = $input['Car_URI'];
                if($id){
                    $Race = (new Race)->where('id',$id)->first();
                    if($Race){
                        $currentEntrants = !empty($Race->entrants) ? json_decode($Race->entrants, true) : [];
                        if (in_array($carUri, $currentEntrants)) {
                            $key = array_search($carUri, $currentEntrants);
                            unset($currentEntrants[$key]);
                        } else {
                            http_response_code(404);
                            echo "Record with Entrants uri $carUri in Race number $id is not exists !!";
                            return;
                        }
                        $Race->entrants = json_encode(array_values($currentEntrants));
                        $Race->save();
                        http_response_code(200);
                        redirect(url('race/'.$Race->id));

                    }
                    else{
                        http_response_code(404);
                        echo "Record with Race number $id Not Found !!";
                        return;
                    }               
                }
                else{
                    http_response_code(400);
                    echo "id field is missing in url !\n";
                    return;
                }
            }
            else{
                http_response_code(400);
                echo "'Car_URI' field is required !\n";
                print_r($_POST);
                return;
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