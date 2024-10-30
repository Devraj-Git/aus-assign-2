<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Race;
use App\Models\Track;
use App\Models\Lap;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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
                // if (preg_match('/car\/(\d+)$/', $carUri, $matches)) {
                //     $carNumber = $matches[1];
                //     if(!(new Car)->where('id',$carNumber)->first()){
                //         http_response_code(400);
                //         echo "Record with Car id $carNumber Not Found !!";
                //         return;
                //     }
                // } else {
                //     http_response_code(400);
                //     echo "The Car_URI should end with 'car/{number}' pattern.\n";
                //     return;
                // }
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

    public function race_qualify($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if($id){
                $Race = (new Race)->where('id',$id)->first();
                if($Race){
                    if(empty($Race->startingPositions)){
                        if(empty($Race->entrants)){
                            http_response_code(404);
                            echo "No Entrants for Qualify !!";
                            return;
                        }
                        $currentEntrants =json_decode($Race->entrants, true);
                        $track = (new Track($Race->track))->select('type')->first()->type;
                        $skill = [];
                        $client = new Client();
                        foreach($currentEntrants as $cars){
                            try {
                                $response = $client->get($cars);
                                $body = $response->getBody()->getContents();
                                $data = json_decode($body, true);
                                $driver_uri = null;
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    foreach ($data as $entry) {
                                        $driver_uri = $entry['driver']['uri'];
                                    }
                                } else {
                                    http_response_code(404);
                                    echo "Response Error From Cars API(Teams) decoding JSON: " . json_last_error_msg();
                                    return;
                                }
                                if($driver_uri){
                                    $response_dri = $client->get($driver_uri);
                                    $body_dri = $response_dri->getBody()->getContents();
                                    $data_dri = json_decode($body_dri, true);
                                    if (json_last_error() === JSON_ERROR_NONE) {
                                        foreach ($data_dri as $entry_dri) {
                                            $skill [] = $entry_dri['skill'][$track];
                                        }
                                    } else {
                                        http_response_code(404);
                                        echo "Response Error From Driver API(Teams) decoding JSON: " . json_last_error_msg();
                                        return;
                                    }
                                }
                                else{
                                    http_response_code(404);
                                    echo "driver_uri missing !!";
                                    return;
                                }
                            
                            } catch (\Exception $e) {
                                http_response_code(404);
                                echo "Error: " . $e->getMessage();
                                return;
                            }
                            
                        }
                        $sortedSkill = $skill;
                        arsort($sortedSkill);
                        $output = [];
                        foreach ($skill as $key => $value) {
                            $output[$key] = array_search($value, array_values($sortedSkill));
                        }
                        $Race->startingPositions = json_encode(array_values($output));
                        $Race->save();
                        http_response_code(200);
                        redirect(url('race/'.$id));
                    }
                    else{
                        http_response_code(404);
                        echo "The startingPositions have already been populated";
                        return;
                    }
                }
                else{
                    http_response_code(404);
                    echo "Record with Race id $id Not Found !!";
                    return;
                }    
            } 
        }
    }
    public function race_lap($id=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $Race = (new Race)->where('id',$id)->first();
                if($Race){
                    $laps = (new Lap)->where('race_id',$id)->get();
                    $this->response($laps);
                }
                else{
                    http_response_code(404);
                    echo "Record with Race id $id Not Found !!";
                    return;
                } 
            }
            else{
                http_response_code(400);
                echo "Id field is required !\n";
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if($id){
                $Race = (new Race)->where('id',$id)->first();
                if($Race){
                    if(empty($Race->entrants)){
                        http_response_code(404);
                        echo "No Entrants for Qualify !!";
                        return;
                    }
                    if(!empty($Race->startingPositions)){
                        $find_number = (new Lap)->select('number')->where('race_id',$id)->get();
                        $number = $find_number ? max(array_map(fn($max) => $max->number, $find_number))+1 : 0;
                        if($number < $Race->tracks()->first()->laps){
                            $entrant = 0;
                            $client = new Client();
                            $currentEntrants =json_decode($Race->entrants, true);
                            foreach($currentEntrants as $cars){
                                $check_crashed_or_not = (new Lap)->where('race_id',$id)->where('entrant',$entrant)->where('crashed','true')->first();
                                if($check_crashed_or_not){
                                    $entrant += 1;
                                    continue;
                                }
                                $lap_url = $cars.'/lap';
                                try {
                                    $get_type_baselap = (new Track($Race->track))->select('type','baseLapTime')->first();
                                    $response = $client->request('GET', $lap_url, [
                                        'query' => [
                                            'baseLapTime' => $get_type_baselap->baseLapTime,
                                            'trackType' => $get_type_baselap->type
                                        ]
                                    ]);
                                    $body = $response->getBody()->getContents();
                                    $data = json_decode($body, true);
                                    
                                    $lap = new Lap;                                
                                    $lap->race_id = $id;
                                    $lap->number = $number;
                                    $lap->entrant = $entrant;
                                    $lap->time =$data['time']+$data['randomness'];
                                    $lap->crashed = $data['crashed'];
                                    $lap->save();                          
                                } catch (\Exception $e) {
                                    http_response_code(404);
                                    echo "Error: " . $e->getMessage();
                                    return;
                                }
                                $entrant += 1;
                            }
                        }
                        else{
                            http_response_code(404);
                            echo "Laps have been Completed.";
                            return;
                        }
                    }
                    else{
                        http_response_code(404);
                        echo "The startingPositions have not been populated yet.";
                        return;
                    }
                }
                else{
                    http_response_code(404);
                    echo "Record with Race id $id Not Found !!";
                    return;
                } 
            }
            else{
                http_response_code(400);
                echo "Id field is required !\n";
            }
        }
    }

    public function lap_leaderboard($id=null,$number=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $Race = (new Race)->where('id',$id)->first();
                if($Race){
                    if($number){
                        $lap_number = $number;
                        $client = new Client();
                        $currentEntrants =json_decode($Race->entrants, true);
                        $entrants_output = [];
                        $entrant = 0;
                        foreach($currentEntrants as $cars){
                            try {
                                $response = $client->get($cars);
                                $body = $response->getBody()->getContents();
                                $data = json_decode($body, true);

                                $driver_uri = $data[0]['driver']['uri'];
                                $response_dri = $client->get($driver_uri);
                                $body_dri = $response_dri->getBody()->getContents();
                                $data_dri = json_decode($body_dri, true);

                                $startingPositions =json_decode($Race->startingPositions, true);
                                $time = $startingPositions[$entrant]*5;
                                $laps = (new Lap)->select('time')->where('race_id',$id)->where('entrant',$entrant)->where('crashed','false')->get();
                                foreach($laps as $lap){
                                    $time += $lap->time;
                                }
                                $entrants_output[0]=[
                                    'number' => $data_dri[0]['number'],
                                    'shortName' => $data_dri[0]['shortName'],
                                    'name' => $data_dri[0]['name'],
                                    'uri' => $cars,
                                    'laps' => count($laps),
                                    'time' => $time,
                                ];
                                print_r($entrants_output);
                            
                            } catch (\Exception $e) {
                                http_response_code(404);
                                echo "Error: " . $e->getMessage();
                                return;
                            }
                            $entrant += 1;

                        }

                    }
                    else{
                        echo "leaderboard.";                    
                    }
                }
                else{
                    http_response_code(404);
                    echo "Record with Race id $id Not Found !!";
                    return;
                }
            }
            else{
                http_response_code(400);
                echo "Id field is required !\n";
                return;
            }

        }
    }
}