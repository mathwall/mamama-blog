<?php

use App\Dispatcher;
use App\Src\Request;


// __ROOT_DIR__ est une constante qui va representer la racine du projet
define("__ROOT_DIR__", __DIR__ . "/../");

// Chargement des autoloads
require_once __ROOT_DIR__ . '/vendor/autoload.php';

// Chargement du Core
require_once __ROOT_DIR__ . '/App/Src/core.php';


$request = new Request();
Dispatcher::redirect($request);