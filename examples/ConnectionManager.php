<?php
include_once '../lib/autoload.php';

use orm\connection\ConnectionManager;

try {

    $connectionManager = ConnectionManager::initialize(function ($config) {
        $config->addConfig('mysql', 'root', '', 'localhost', 'local_controlook', 'local', 3306);
        $config->addConfig('mysql', 'root', '', 'localhost', 'local_controlook', 'teste', 3306);

        $config->setDefault('local');
    });

    $connectionManager->open('local');
}catch (Exception $e){
    echo $e->getMessage();
}