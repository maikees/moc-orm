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
 * @var This is an string
 *
 *  3. Set the static attribute $table_name in your model
 * @var This is an string
 *
 *  4. Open transaction using method beginTransaction using $connection->beginTransaction()
 *
 *  5. Perform the procedures within the transaction
 *
 *  6. Rollback transaction
 *      - Commit send your data to database
 * @method $connection->commitTransaction()
 *
 *  7. Commit transaction
 *      - Rollback Not send your data to database
 * @method $connection->rollbackTransaction()
 *
 */

use MocOrm\Connection\ConnectionManager;
use MocOrm\Support\Transaction;
use MocOrm\Usage\UsageModel;

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
         *      - $connection->addConfig('driver', 'user', 'password', 'host', 'database', 'connectionName', 'port', charset, schema);
         *      - Driver options ['mysql', 'pgsql'] -- Mysql, postgres
         * @return Connection
         */
        $connection->addConfig(
            'pgsql',
            'user',
            'pass',
            'host',
            'database',
            'name',
            'port',
            'char',
            'schema');
    });

    /**
     *  4. Open transaction using Object Transaction using $connection->beginTransaction()
     */

    $transactionBack = new Transaction(function () {
        $usage55 = UsageModel::create(['id' => '55', 'nome' => 'Teste rollback 1']);
        $usage56 = UsageModel::create(['id' => '56', 'nome' => 'Teste rollback 2']);
        $usage57 = UsageModel::create(['id' => '57', 'nome' => 'Teste rollback 3']);

        /**
         *  6. Rollback transaction
         *  All throw new Expetion does the rollback in this Transaction
         *  If returns false does the rollback in this Transaction
         *      - Rollback Not send your data to database
         * @method $connection->rollbackTransaction()
         */
        throw new \Exception('Error to rollback.');
    });

    if ($transactionBack->hasError()) {
        echo $transactionBack->getError()->getMessage();
        echo '<br />';
    }

    $transactionCommit = new Transaction(function () {
        $usage58 = UsageModel::create(['id' => '58', 'nome' => 'Teste commit 1']);
        $usage59 = UsageModel::create(['id' => '59', 'nome' => 'Teste commit 2']);
        $usage60 = UsageModel::create(['id' => '60', 'nome' => 'Teste commit 3']);

        /**
         * 7. Commit transaction
         * If returns
         * If returns true does the auto commit in this Transaction
         *      - Commit send your data to database
         * @method $connection->commitTransaction()
         */
    });

    if (!$transactionCommit->hasError()) {
        echo 'Commit success.';
    }
    echo '<br />';
} catch (\Exception $e) {
    /**
     * All Exceptions
     */
    echo $e->getMessage();
}