#Connection
 * Usage connection example.
 *
  1. Extends your model to class Model, using namespace orm\model\Model;
 
  2. Set the namespace to connection
 
  3. Initialize the connection using static method Connection::initialize()
  @return Connection
 
  4. Add the configurations using the method addConfig, accepts various configurations
 *      Arguments:
 *      - $connection->addConfig('driver', 'user', 'password', 'host', 'database', 'connectionName', 'port');
 *      - Driver options ['mysql', 'pgsql'] -- Mysql, postgres
 * @return Connection
 *
  5. Set connection for active using the method setConnection
        * $connection->setConnection('connectionName');
 * @return Connection
 
  6. if needed change the connection using the method changeConnection
 *      - $connection->setConnection('connectionName');
 * @return Connection
 
  7. Get current connection using the method getCurrentConnectionName
 *      - $connection->getCurrentConnectionName();
 * @return String Connection name
 
  8. Get all previous settings, using method getConfig.
 *      - $connection->getConfig();
 * @return array on connection string
 
  9. Get last performed query using method getLastPerformedQuery()
 * @return array
 
  10. Get all performed query using method getPerformedQuery()
 * @return array