<?php

use App\Src\Router;

// __ROOT_DIR__ est une constante qui va representer la racine du projet
define("__ROOT_DIR__", __DIR__ . "/../");

// Chargement des autoloads
require_once __ROOT_DIR__ . '/vendor/autoload.php';

Router::test();

die("Tout a faire ici");

