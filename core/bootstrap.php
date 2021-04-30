<?php

use Core\App;
use Core\database\QueryBuilder;
use Core\database\Connection;

App::bind('config', require 'config.php');


//$config = App::get('config');
// require 'core/database/Connectoion.php';
// require 'core/database/QueryBuilder.php';
// require 'core/router.php';
// require 'core/Request.php';



App::bind('database',  new QueryBuilder(
    Connection::make(App::get('config')['database'])

));

App::bind('jwtKey', 'AminulExam');

function view($name, $data = [])
{
    extract($data);
    return require "views/{$name}.view.php";
}

function redirect($path)
{
    header("Location: /{$path}");
}
