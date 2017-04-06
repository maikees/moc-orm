<?php

namespace orm\connection;

class Connection
{
    /**
     * This instance object
     * @var Connection.
     */
    private static $_instance;

    /**
     * This is instance current connect object connected
     * @var PDO
     */
    private $_connection;

    /**
     * @connectionString array of string This attribute is internal for save the connection string
     */
    private $_connectionString = [];

    /**
     * @currentConnectionName string of the current connection name
     */
    private $_currentConnectionName;

    /**
     * @username array of string This attribute is internal for save the connection usernames
     */
    private $_username = [];

    /**
     * @username array of string This attribute is internal for save the connection passwords
     */
    private $_password = [];

    /**
     * List of drivers sets on connections
     * @var $_driver
     */
    private $_driver = [];

    /**
     * This save all query orm use.
     * @var query string
     */
    private $performed_query = [];

    /**
     * Constant to define set accepted drivers.
     */
    const DRIVERS = [
        "msyql",
        "pgsql",
    ];

    /**
     * Initialize the object or return this object if have value set in attribute $_instance
     * @return Connection
     */
    final public static function initialize()
    {
        if (!self::$_instance) {
            self::$_instance = new Connection();
        }

        return self::$_instance;
    }

    /**
     * Create configuration from connection
     * @param string $driver The driver from connection
     * @param string $username The username from connection
     * @param string $password The password from connection
     * @param string $host The host from connection
     * @param string $database The database from connection
     * @param string $connectionName The connection name from connection
     * @param integer $port The port from connection
     * @return $this This object for other iterators
     * @throws \Exception case one or some elements on parameters are invalid
     */
    public function addConfig($driver = 'mysql', $username = "root", $password = null, $host = "localhost", $database = null, $connectionName = null, $port = null)
    {

        #Begin: Verify if all parameters send is valid.
        if (!is_string($driver) and !array_search($driver, DRIVERS)) throw new \Exception("The driver $driver don't supported.");
        if (!is_string($username)) throw new \Exception("Invalid username.");
        if (!is_string($password)) throw new \Exception("Invalid password.");
        if (!is_string($host)) throw new \Exception("Invalid host.");
        if (!is_string($database)) throw new \Exception("Invalid database name.");
        if (!is_string($connectionName)) throw new \Exception("Invalid connection name.");

        $port = is_null($port) ? '' : (int)$port;
        if (!is_null($port) && !is_int($port)) throw new \Exception("Invalid port format.");

        #Constructor of the connection string
        $this->_connectionString[$connectionName] = "$driver:host=$host;dbname=$database;port=$port;";
        $this->_username[$connectionName] = $username;
        $this->_password[$connectionName] = $password;
        $this->_driver[$connectionName] = $driver;

        $this->setConnection($connectionName);

        return $this;
    }

    /**
     * Set the current connection on connection name.
     * @param string $connectionName name on connection
     * @return $this This object from other interator
     * @throws \Exception if the connect name haven't set;
     */
    public function setConnection($connectionName)
    {
        if (!is_string($connectionName)) throw new \Exception("Invalid connection name.");

        if (array_key_exists($connectionName, $this->_connectionString)) {

            $this->_currentConnectionName = $connectionName;
        } else {
            throw new \Exception("The connection name $connectionName is not set.");
        }

        try {
            $this->connection();
        } catch (Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    /**
     * Get all connection string
     * @return array if have connection but not have this method return null
     */
    final public function getConfigs()
    {
        return $this->_connectionString;
    }

    /**
     * Get name on current connection
     * @return string name on current connection or null if don't have
     */
    final public function getCurrentConnectionName()
    {
        return $this->_currentConnectionName;
    }

    /**
     * Initialize the connection
     * @return $this
     * @throws \Exception
     */
    public function connection()
    {
        if (is_null($this->getCurrentConnectionName())) throw new \Exception('Conexão não setada.');

        try {
            $connectionName = $this->getCurrentConnectionName();

            $this->_connection = new \PDO(
                $this->_connectionString[$connectionName],
                $this->_username[$connectionName],
                $this->_password[$connectionName]
            );

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * @return query
     */
    public function getPerformedQuery()
    {
        return $this->performed_query;
    }

    /**
     * @return query
     */
    public function getLastPerformedQuery()
    {
        return end($this->performed_query);
    }

    /**
     * @param String $query
     * @param String $time
     * @return $this
     */
    public function setPerformedQuery(String $query, String $time)
    {
        $this->performed_query[] = ['query' => $query, 'time' => $time];
        return $this;
    }

    /**
     * @return array
     */
    public function getDriver()
    {
        return $this->_driver;
    }

    /**
     * Open transaction for insert, update, delete.
     * @return $this
     */
    final public function beginTransaction()
    {
        $this->_connection->beginTransaction();
        return $this;
    }

    /**
     * Using commit to all actions executed after begin transaction
     * @return $this
     */
    final public function commitTransaction()
    {
        $this->_connection->commit();
        return $this;
    }

    /**
     * Using rollback to all actions executed after begin transaction
     * @return $this
     */
    final public function rollbackTransaction()
    {
        $this->_connection->rollBack();
        return $this;
    }

    /**
     * Change schema on postgres
     * @param $schema schema name
     * @return $this
     */
    final public function changeSchema($schema = null){
        if(!is_string($schema)) throw new \Exception('The parameter don\'t is an String.');

        $this->getConnection()->exec("SET search_path TO '$schema';");
        return $this;
    }

}
