<?php

use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\LoginController;
use App\Controllers\LogoutController;
use App\Controllers\PermissionController;
use App\Controllers\AccessController;
use App\Controllers\DiscordController;
use App\System\Core\Router;

$router = new Router();

$router->get('/', HomeController::class, 'index');

$router->get('/403', UserController::class, '_403');
$router->get('/404', UserController::class, '_404');
$router->get('/login', UserController::class, 'login');

$router->post('/login', LoginController::class, 'check');
$router->get('/logout', LogoutController::class, 'index');

$router->get('/register', UserController::class, 'register');
$router->post('/register', UserController::class, 'register');

$router->get('/permission', PermissionController::class, 'index');
$router->post('/update-role', PermissionController::class, 'roleUpdate');

$router->get('/access', AccessController::class, 'index');
$router->post('/access', AccessController::class, 'index');
$router->post('/changeView', AccessController::class, 'changeView');


$router->get('/discord/connect', DiscordController::class, 'connect');
$router->get('/discord', DiscordController::class, 'callback');
$router->get('/discord/showProfile', DiscordController::class, 'showProfile');


$router->dispatch();