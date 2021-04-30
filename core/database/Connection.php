<?php

namespace Core\database;

class Connection
{

    public static function make($config)
    {
        try {
            // return new PDO('mysql:host=127.0.0.1;dbname=wedev', 'aminul', 'Aminul#123');
            return new \PDO(
                $config['connection'] . ';dbname=' . $config['name'],
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }
}
