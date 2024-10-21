<?php

const BASEPATH = __DIR__;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require  BASEPATH . '/vendor/autoload.php';

$router = require  BASEPATH . '/src/Routes/index.php';