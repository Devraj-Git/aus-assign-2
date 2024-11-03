<?php

use App\Controllers\TrackController;
use App\Controllers\RaceController;
use App\Controllers\ResponseController;
use App\System\Core\Router;

$router = new Router();

$router->get('/track', TrackController::class, 'index');
$router->get('/track/scrape', TrackController::class, 'scrape');
$router->get('/track/{id}', TrackController::class, 'index');
$router->post('/track', TrackController::class, 'index');
$router->delete('/track/{id}', TrackController::class, 'index');
$router->get('/track/{id}/races', TrackController::class, 'track_races');
$router->post('/track/{id}/races', TrackController::class, 'track_races');




$router->get('/race', RaceController::class, 'index');
$router->get('/race/{id}', RaceController::class, 'index');
$router->get('/race/{id}/entrant', RaceController::class, 'race_entrant');    
$router->post('/race/{id}/entrant', RaceController::class, 'race_entrant');    
$router->delete('/race/{id}/entrant', RaceController::class, 'race_entrant');
$router->post('/race/{id}/qualify', RaceController::class, 'race_qualify');
$router->get('/race/{id}/lap', RaceController::class, 'race_lap');
$router->post('/race/{id}/lap', RaceController::class, 'race_lap');

$router->get('/race/{id}/lap/{number}', RaceController::class, 'lap_leaderboard');
$router->get('/race/{id}/leaderboard', RaceController::class, 'lap_leaderboard');


$router->get('/403', ResponseController::class, '_403');
$router->get('/404', ResponseController::class, '_404');

$router->dispatch();