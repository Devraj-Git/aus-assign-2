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
    public function __construct() {
        $this->checkAuth();
    }
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
                    $this->error_400("Record with Race number $id Not Found !!");
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
                    $this->error_400("Record with Race $id Not Found !!");
                }
            }
            else{
                $this->error_400("id field is required !");
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['Car_URI'])){
                $carUri = $_POST['Car_URI'];
                if($id){
                    $Race = (new Race)->where('id',$id)->first();
                    if($Race){
                        $currentEntrants = !empty($Race->entrants) ? json_decode($Race->entrants, true) : [];
                        if (!in_array($carUri, $currentEntrants)) {
                            $currentEntrants[] = $carUri;
                        } else {
                            $this->error_400("Record with Entrants uri $carUri in Race number $id is already exists !!");
                        }
                        $Race->entrants = json_encode(array_values($currentEntrants));
                        $Race->save();
                        redirect(url('race/'.$Race->id));

                    }
                    else{
                        $this->error_400("Record with Race id $id Not Found !!");
                    }               
                }
                else{
                    $this->error_400("id field is missing in url !");
                }
            }
            else{
                $this->error_400("'Car_URI' field is required !");
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
                            $this->error_400("Record with Entrants uri $carUri in Race number $id is not exists !!");
                        }
                        $Race->entrants = json_encode(array_values($currentEntrants));
                        $Race->save();
                        redirect(url('race/'.$Race->id));

                    }
                    else{
                        $this->error_404("Record with Race number $id Not Found !!");
                    }               
                }
                else{
                    $this->error_400("id field is missing in url !");
                }
            }
            else{
                $this->error_400("'Car_URI' field is required !");
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
                            $this->error_404("No Entrants for Qualify !!");
                        }
                        $currentEntrants =json_decode($Race->entrants, true);
                        $track = (new Track($Race->track))->select('type')->first()->type;
                        $skill = [];
                        $client = new Client();
                        foreach($currentEntrants as $cars){
                            try {
                                $response = $client->get($cars);
                                $body = $response->getBody()->getContents();
                                $data = json_decode($body, true)['result'];
                                $driver_uri = null;
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    foreach ($data as $entry) {
                                        $driver_uri = $entry['driver']['uri'];
                                    }
                                } else {
                                    $this->error_404("Response Error From Cars API(Teams) decoding JSON: " . json_last_error_msg(),false);
                                }
                                if($driver_uri){
                                    $response_dri = $client->get($driver_uri);
                                    $body_dri = $response_dri->getBody()->getContents();
                                    $data_dri = json_decode($body_dri, true)['result'];
                                    if (json_last_error() === JSON_ERROR_NONE) {
                                        foreach ($data_dri as $entry_dri) {
                                            $skill [] = $entry_dri['skill'][$track];
                                        }
                                    } else {
                                        $this->error_404("Response Error From Driver API(Teams) decoding JSON: " . json_last_error_msg(),false);
                                    }
                                }
                                else{
                                    $this->error_404("driver_uri missing !!");
                                }
                            
                            } catch (\Exception $e) {
                                // $this->error_404("Error: " . $e->getMessage(),false);
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
                        $this->error_404("The startingPositions have already been populated");
                    }
                }
                else{
                    $this->error_404("Record with Race id $id Not Found !!");
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
                    $this->error_404("Record with Race id $id Not Found !!");
                } 
            }
            else{
                $this->error_400("Id field is required !");
            }
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if($id){
                $Race = (new Race)->where('id',$id)->first();
                if($Race){
                    if(empty($Race->entrants)){
                        $this->error_404("No Entrants for Qualify !!");
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
                                    $data = json_decode($body, true)['result'];
                                    
                                    $lap = new Lap;                                
                                    $lap->race_id = $id;
                                    $lap->number = $number;
                                    $lap->entrant = $entrant;
                                    $lap->time =$data['time']+$data['randomness'];
                                    $lap->crashed = $data['crashed'];
                                    $lap->save();                          
                                } catch (\Exception $e) {
                                    // $this->error_404("Error: " . $e->getMessage(),false);
                                }
                                $entrant += 1;
                            }
                        }
                        else{
                            $this->error_404("Laps have been Completed.");
                        }
                    }
                    else{
                        $this->error_404("The startingPositions have not been populated yet.");
                    }
                }
                else{
                    $this->error_404("Record with Race id $id Not Found !!");
                } 
            }
            else{
                $this->error_400("Id field is required !");
            }
        }
    }

    public function lap_leaderboard($id=null,$number=null)
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if($id){
                $Race = (new Race)->where('id',$id)->first();
                if($Race){
                    $lap_number = $number;
                    $client = new Client();
                    $currentEntrants =json_decode($Race->entrants, true);
                    $entrants_output = [];
                    $entrant = 0;
                    foreach($currentEntrants as $cars){
                        try {
                            $response = $client->get($cars);
                            $body = $response->getBody()->getContents();
                            $data = json_decode($body, true)['result'];

                            $driver_uri = $data[0]['driver']['uri'];
                            $response_dri = $client->get($driver_uri);
                            $body_dri = $response_dri->getBody()->getContents();
                            $data_dri = json_decode($body_dri, true)['result'];

                            $startingPositions =json_decode($Race->startingPositions, true);
                            $time = $startingPositions[$entrant]*5;
                            $laps = (new Lap)->select('time')->where('race_id',$id)->where('entrant',$entrant)->where('crashed','false')->get();
                            $count_laps = count($laps);
                            if($number){
                                $laps = (new Lap)->select('time')->where('race_id',$id)->where('entrant',$entrant)->where('number',$number)->where('crashed','false')->get();
                                if(!$laps){
                                    $time = 0;
                                }
                            }
                            foreach($laps as $lap){
                                $time += $lap->time;
                            }
                            $entrants_output[$entrant]=[
                                'number' => $data_dri[0]['number'],
                                'shortName' => $data_dri[0]['shortName'],
                                'name' => $data_dri[0]['name'],
                                'uri' => $cars,
                                'laps' => $count_laps,
                                'time' => ($count_laps == 0) ? 0 : $time,
                            ];
                        
                        } catch (\Exception $e) {
                            // $this->error_404("Error: " . $e->getMessage(),false);
                        }
                        $entrant += 1;

                    }
                    usort($entrants_output, function($a, $b) {
                        if ($a['laps'] != $b['laps']) {
                            return $b['laps'] - $a['laps'];
                        }
                        return $a['time'] <=> $b['time'];
                    });
                    if($number){
                        $formatted_output = [
                            "lap" => $number,
                            "entrants" => $entrants_output
                        ];
                    }
                    else{
                        $formatted_output = [
                            "entrants" => $entrants_output
                        ];
                    }
                    $this->response($formatted_output);               
                }
                else{
                    $this->error_404("Record with Race id $id Not Found !!");
                }
            }
            else{
                $this->error_400("Id field is required !");
            }

        }
    }
}