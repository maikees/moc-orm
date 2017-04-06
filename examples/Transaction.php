<?php

include_once '../lib/autoload.php';
include_once 'UsageModel.php';

/**
 *  This exemple need a model
 *  After example use Connection
 *  Using example Create and Delete
 *
 *  1. Extends your model to class Model, using namespace orm\model\Model
 *
 *  2. Set the static attribute $primary_key in your model
 *      @var This is an string
 *
 *  3. Set the static attribute $table_name in your model
 *      @var This is an string
 *
 *  4. Open transaction using method beginTransaction using $connection->beginTransaction()
 *
 *  5. Perform the procedures within the transaction
 *
 *  6. Rollback transaction
 *      - Commit send your data to database
 *          @method $connection->commitTransaction()
 *
 *  7. Commit transaction
 *      - Rollback Not send your data to database
 *          @method $connection->rollbackTransaction()
 *
 */

use orm\connection\Connection;
use quickcooffe\usage\UsageModel;

try {

    /**
     * Initialize the connection using static method Connection::initialize()
     * @return Connection
     */
    $connection = Connection::initialize();

    /**
     *  Add the configurations using the method addConfig, accepts various configurations
     *      Arguments:
     *      - $connection->addConfig('driver', 'user', 'password', 'host', 'database', 'connectionName', 'port');
     *      - Driver options ['mysql', 'pgsql'] -- Mysql, postgres
     * @return Connection
     */
    $connection->addConfig('mysql', 'root', '', 'localhost', 'local_controlook', 'local', 3306);
    $connection->addConfig('pgsql', 'postgres', '123456', 'localhost', 'local_controlook', 'postgres_local', 5432);

    /**
     * Set connection for active using the method setConnection
     *      - $connection->setConnection('connectionName');
     * @return Connection
     */
    $connection->setConnection('postgres_local');

    /**
     *  4. Open transaction using method beginTransaction using $connection->beginTransaction()
     */
    $connection->beginTransaction();

    $usage55 = UsageModel::create(['id2' => '55', 'nome' => 'Teste rollback 1']);
    $usage56 = UsageModel::create(['id2' => '56', 'nome' => 'Teste rollback 2']);
    $usage57 = UsageModel::create(['id2' => '57', 'nome' => 'Teste rollback 3']);

    /**
     *  6. Rollback transaction
     *      - Rollback Not send your data to database
     *          @method $connection->rollbackTransaction()
     */
    $connection->rollbackTransaction();

    $connection->beginTransaction();

    $usage58 = UsageModel::create(['id2' => '58', 'nome' => 'Teste commit 1']);
    $usage59 = UsageModel::create(['id2' => '59', 'nome' => 'Teste commit 2']);
    $usage60 = UsageModel::create(['id2' => '60', 'nome' => 'Teste commit 3']);

    /**
     * 7. Commit transaction
     *      - Commit send your data to database
     *          @method $connection->commitTransaction()
     */
    $connection->commitTransaction();


} catch (Exception $e) {
    /**
     * All Exceptions
     */
    echo $e->getMessage();
}