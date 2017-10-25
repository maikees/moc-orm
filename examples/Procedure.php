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
 *  4. Execute the previous defined procedure.
 *      @var String The name of procedure
 *      @var Array of params
 *      @return result the procedure
 *
 */

use MocOrm\Connection\ConnectionManager;
use MocOrm\Usage\UsageModel;

try {

    /**
     * Initialize the connection using static method ConnectionManager::initialize()
     * @param \Closure $connection
     * @return \ConnectionManager
     */
    $connectionManager = ConnectionManager::initialize(function ($config) {
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
    });

    /**
     *  4. Execute the previous defined procedure.
     *      @var String The name of procedure
     *      @var Array of params
     *      @return result the procedure
     */
    $usage = UsageModel::procedure('criar_usuario', ['Teste procedure apelido']);
    var_dump($usage);
    if($usage){
        var_dump($usage);
    }else{
        echo 'The procedure not executed.';
    }

} catch (Exception $e) {
    /**
     * All Exceptions
     */
    echo $e->getMessage();
}