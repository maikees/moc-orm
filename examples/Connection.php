<?php

include_once '../lib/autoload.php';

/**
 * Usage connection example.
 *
 * 1. Extends your model to class Model, using namespace orm\model\Model;
 *
 * 2. Set the namespace to connection
 *
 * 3. Initialize the Connection Manager using static method ConnectionManager::initialize(closure(connection))
 * @return ConnectionManager
 *
 * 4. Add the configurations using the method addConfig, into closure, accepts various configurations
 *      Arguments:
 *      - $connection->addConfig('driver', 'user', 'password', 'host', 'database', 'connectionName', 'port');
 *      - Driver options ['mysql', 'pgsql'] -- Mysql, postgres
 * @return $connectionManager
 *
 * 5. Set connection for active using the method open
 *      - $connectionManager->open('connectionName');
 * @return $connectionManager
 *
 * 6. if needed change the connection using the method change
 *      - $connectionManager->change('connectionName');
 * @return $connectionManager
 *
 * 7. Get current connection using the method current()->getCurrentConnectionName
 *      - $connectionManager->current()->getCurrentConnectionName();
 * @return String Connection name
 * 8. Get all previous settings, using method getConfig.
 *      - $connectionManager->current()->getConfig();
 * @return array on connection string
 *
 * 9. Get last performed query using method current()->getLastPerformedQuery()
 *      - $connectionManager->current()->getLastPerformedQuery();
 * @return array
 *
 * 10. Get all performed query using method current()->getPerformedQuery()
 *      - $connectionManager->current()->getPerformedQuery();
 * @return array
 */

/**
 * 1. Set the namespace to connection
 */
use orm\connection\ConnectionManager;

try {

    /**
     * 3. Initialize the connection using static method ConnectionManager::initialize()
     * @param \Closure $connection
     * @return \ConnectionManager
     */
    $connectionManager = ConnectionManager::initialize(function ($connection) {
        /**
         * 4. Add the configurations using the method addConfig, accepts various configurations
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
     * 5. Set connection for active using the method setConnection
     *      - $connection->setConnection('connectionName');
     * @return Connection
     */
    $connectionManager::open('postgres_local');
    $connectionManager::open('local');

    /**
     * 6. if needed change the connection using the method change
     *      -     $connectionManager::change('local');

     * @return Connection
     */
    $connectionManager::change('local');

    /**
     * 7. Get current connection using the method current()->getCurrentConnectionName();
     *      - $connectionManager->current()->getCurrentConnectionName();
     * @return String Connection name
     */
    $connectionManager->current()->getCurrentConnectionName();

    /**
     * 8. Get all previous settings, using method current()->getConfigs().
     *      - $connectionManager->current()->getConfigs();
     * @return array on connection string
     */
    $connectionManager->current()->getConfigs();

    /**
     * 9. Get last performed query using method current()->getLastPerformedQuery()
     */
    $connectionManager->current()->getLastPerformedQuery();

    /**
     * 10. Get all performed query using method current()->getPerformedQuery()
     */
    $connectionManager->current()->getPerformedQuery();


} catch (Exception $e) {
    /**
     * All Exceptions
     */
    echo $e->getMessage();
}
?>