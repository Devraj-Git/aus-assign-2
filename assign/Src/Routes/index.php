<?php

use App\Controllers\DriverController;
use App\Controllers\CarController;
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
$router->delete('/car/{id}', CarController::class, 'index');


$router->get('/403', ResponseController::class, '_403');
$router->get('/404', ResponseController::class, '_404');
// $router->get('/login', UserController::class, 'login');

// $router->post('/login', LoginController::class, 'check');
// $router->get('/logout', LogoutController::class, 'index');

// $router->get('/register', UserController::class, 'register');
// $router->post('/register', UserController::class, 'register');

// $router->get('/permission', PermissionController::class, 'index');
// $router->post('/update-role', PermissionController::class, 'roleUpdate');

// $router->get('/access', AccessController::class, 'index');
// $router->post('/access', AccessController::class, 'index');
// $router->post('/changeView', AccessController::class, 'changeView');


// $router->get('/discord/connect', DiscordController::class, 'connect');
// $router->get('/discord', DiscordController::class, 'callback');
// $router->get('/discord/showProfile', DiscordController::class, 'showProfile');


$router->dispatch();