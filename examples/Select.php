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
 *  4. Dynamic select
 *      @function select
 *          Are the colunms of table
 *      @function where
 *          Are the initial condition
 *      @function and
 *          Aditional condition
 *      @function or
 *          Aditional condition
 *      @function orderBy
 *          Aditional condition
 *      @function joins
 *          Aditional condition
 *      @function done
 *          Execute the query
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
     *  4. Dynamic select
     *      @function select
     *          Are the colunms of table
     *      @function where
     *          Are the initial condition
     *      @function and
     *          Aditional condition
     *      @function or
     *          Aditional condition
     *      @function orderBy
     *          Aditional condition
     *      @function joins
     *          Aditional condition
     *      @function done
     *          Execute the query
     */
    $usage = UsageModel::select('*')
                    ->where('id', 192)
                    ->or('nome', 'Teste Save')
                    ->and('id', 10)
                    ->orderBy('nome', 'DESC')
                    ->done();


    /**
     * 5. Joins example
     */
    $result =   UsageModel::select('chave_composta.id as chave_id,
                                chave_composta.id2 as chave_id2,
                                chave_composta.nome as chave_nome,
                                tb_usuarios.id as usuario_id,
                                tb_usuarios.nome as usuario_nome')
        ->leftJoin('tb_usuarios ON tb_usuarios.id = chave_composta.id2')
        ->orderBy('chave_composta.nome', 'DESC')
        ->done();
    if(count($usage) > 0){
        var_dump($usage);
    }else{
        echo 'Haven\'t data for this Model.';
    }

} catch (Exception $e) {
    /**
     * All Exceptions
     */
    echo $e->getMessage();
}