<?php
include_once '../lib/autoload.php';

use mocorm\connection\ConnectionManager;

try {


    $connectionManager = ConnectionManager::inicialize(function ($connection) {
        $connection->addConfig('mysql', 'root', '', 'localhost', 'local_controlook', 'local', 3306);
        $connection->addConfig('mysql', 'root', '', 'localhost', 'local_controlook', 'teste', 3306);

        return $connection;
    });

    $connectionManager->open('local');
    var_dump($connectionManager->current());



}catch (Exception $e){
    echo $e->getMessage();
}