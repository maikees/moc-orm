<?php

use MocOrm\Connection\ConnectionManager;
use MocOrm\Support\Log;

include_once '../vendor/autoload.php';
include_once '../examples/UsageModel.php';


$connectionManager = ConnectionManager::initialize(function ($config) {
    /**
     * Add the configurations using the method addConfig, accepts various configurations
     *      Arguments:
     *      - $connection->addConfig('driver', 'user', 'password', 'host', 'database', 'connectionName', 'port', charset, schema);
     *      - Driver options ['mysql', 'pgsql'] -- Mysql, postgres
     * @return $config Config
     */
    $config->setDriver('ConnectionName','pgsql');
    $config->setSettings('ConnectionName', [
        'host' => 'hostIp',
        'database' => 'postgres',
        'port' => 'Port'
    ]);
    $config->setUsername('ConnectionName','username');
    $config->setPassword('ConnectionName','password');
    $config->setCharset('ConnectionName', '');
    $config->setSchema('ConnectionName','schema');

    /**
     * Default logger is disabled when enabling this works:
     * OnSave, OnUpdate, OnDelete
     * If enable the logger create the table on file pgsqlLogger.sql
     * This file is into directory 'Dependences'
     */
    $config->appEnableLogger();
});

$logs = Log::all();

var_dump($logs);