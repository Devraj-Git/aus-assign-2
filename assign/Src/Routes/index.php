<?php

use App\Controllers\DriverController;
use App\Controllers\CarController;
use App\Controllers\TrackController;
use App\Controllers\ResponseController;
use App\System\Core\Router;

$router = new Router();

$router->get('/driver', DriverController::class, 'index');
$router->get('/driver/{id}', DriverController::class, 'index');
$router->post('/driver', DriverController::class, 'index');
$router->put('/driver/{id}', DriverController::class, 'index');
$router->delete('/driver/{id}', DriverController::class, 'index');



$router->get('/car', CarController::class, 'index');
$router->get('/car/{id}', CarController::class, 'index');
$router->get('/car/{id}/driver', CarController::class, 'car_driver');
$router->post('/car', CarController::class, 'index');
$router->put('/car/{id}', CarController::class, 'index');
$router->put('/car/{id}/driver', CarController::class, 'car_driver');
$router->delete('/car/{id}', CarController::class, 'index');
$router->delete('/car/{id}/driver', CarController::class, 'car_driver');

$router->get('/car/{id}/lap', CarController::class, 'lap');




$router->get('/track', TrackController::class, 'index');
$router->get('/track/{id}', TrackController::class, 'index');
$router->post('/track', TrackController::class, 'index');
$router->delete('/track/{id}', TrackController::class, 'index');

$router->get('/track/{id}/races', TrackController::class, 'track_races');
$router->post('/track/{id}/races', TrackController::class, 'track_races');
$router->get('/track/scrape', TrackController::class, 'scrape');


$router->get('/403', ResponseController::class, '_403');
$router->get('/404', ResponseController::class, '_404');

$router->dispatch();