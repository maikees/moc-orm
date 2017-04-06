<?php

include_once '../lib/autoload.php';
include_once 'UsageModel.php';

/**
 *  This exemple need a model
 *  After example use Connection
 *
 *  1. Extends your model to class Model, using namespace orm\model\Model
 *
 *  2. Set the static attribute $primary_key in your model
 *      @var This is an string
 *
 *  3. Set the static attribute $table_name in your model
 *      @var This is an string
 *
 *  4. Create data in database on values set from array
 *      @var Array This is one array, the mirror on database table
 *      @return true if save success
 *      @return false if save not success
 *
 */

use orm\connection\ConnectionManager;
use quickcooffe\usage\UsageModel;

try {

    /**
     * Initialize the connection using static method ConnectionManager::initialize()
     * @param \Closure $connection
     * @return \ConnectionManager
     */
    $connectionManager = ConnectionManager::initialize(function ($connection) {
        /**
         * Add the configurations using the method addConfig, accepts various configurations
         *      Arguments:
         *      - $connection->addConfig('driver', 'user', 'password', 'host', 'database', 'connectionName', 'port');
         *      - Driver options ['mysql', 'pgsql'] -- Mysql, postgres
         * @return Connection
         */
        $connection->addConfig('mysql', 'root', '', 'localhost', 'local_controlook', 'local', 3306);
        $connection->addConfig('pgsql', 'postgres', '123456', 'localhost', 'local_controlook', 'postgres_local', 5432);

        return $connection;
    });

    /**
     *  4. Create data in database on values set from array
     *      @var Array This is one array, the mirror on database table
     *      @return true if save success
     *      @return false if save not success
     */
    $usage = UsageModel::create(['id2' => '55', 'nome' => 'Teste create']);

    if($usage){
        echo 'Id: '.$usage->id.'<br />';
        echo 'Nome: '.$usage->nome.'<br />';
    }else{
        echo 'This data haven\'t save.';
    }

} catch (Exception $e) {
    /**
     * All Exceptions
     */
    echo $e->getMessage();
}