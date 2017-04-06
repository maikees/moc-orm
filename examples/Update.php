<?php

include_once '../lib/autoload.php';
include_once 'UsageModel.php';

/**
 *  This exemple need a model
 *  After example use Connection
 *  This example need of primary key defined and have data in attribute
 *
 *  1. Extends your model to class Model, using namespace orm\model\Model
 *
 *  2. Set the static attribute $primary_key in your model
 *      @var This is an string
 *
 *  3. Set the static attribute $table_name in your model
 *      @var This is an string
 *
 *  4. For search your data on primary key use the static function find
 *      - The first argument is the primary key
 *      @var integer
 *      @return Array with object if exists the data
 *      @return Null if not exists the data
 *
 *  4.1. Modify the attributes your needed change
 *
 *  4.2. Use method save in your object
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
     *  4. For search your data on primary key use the static function find
     *      - The first argument is the primary key
     *      @return Array with object if exists the data
     *      @return Null if not exists the data
     */
    $usage = UsageModel::find(246);

    /**
     *  4.1. Modify the attributes your needed change
     */
    $usage->nome = 'Edit your data from ORM.';

    /**
     *  4.2. Use method save in your object
     *      @return model instantiated if save success
     *      @return false if save not success
     */
    $result = $usage->save();

    if($result){
        echo 'Data Edited.';
    }else{
        echo 'Haven\'t data for this ID.';
    }

} catch (Exception $e) {
    /**
     * All Exceptions
     */
    echo $e->getMessage();
}