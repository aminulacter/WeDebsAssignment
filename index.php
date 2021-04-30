<?php

use Core\Router;
use Core\Request;

require 'vendor/autoload.php';
require 'core/bootstrap.php';


$router = new Router;


//require 'routes.php';

//require $router->direct($uri);
Router::load('routes.php')
    ->direct(Request::uri(), Request::method());
//die($path);
//require __DIR__ . $path;
