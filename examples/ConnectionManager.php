<?php
include_once '../lib/autoload.php';

use MocOrm\Connection\ConnectionManager;

try {


    $connectionManager = ConnectionManager::inicialize(function ($config) {
        /**
         * Add the configurations using the method addConfig, accepts various configurations
         *      Arguments:
         *      - $connection->addConfig('driver', 'user', 'password', 'host', 'database', 'connectionName', 'port', charset, schema);
         *      - Driver options ['mysql', 'pgsql'] -- Mysql, postgres
         * @return Connection
         */
        $config->addConfig(
            'mysql',
            'user',
            'pass',
            'host',
            'database',
            'name',
            'port',
            'char');

        $config->addConfig(
            'pgsql',
            'user',
            'pass',
            'host',
            'database',
            'name',
            'port',
            'char',
            'schema');

        /**
         * Or set the separate attributes.
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
         * Default logger if enable
         * OnSave, OnUpdate, OnDelete
         */
        $config->appEnableLogger();
    });

    $connectionManager->open('local');
    var_dump($connectionManager->current());



}catch (Exception $e){
    echo $e->getMessage();
}