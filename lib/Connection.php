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
     * @currentConnectionName string of the current connection name
     */
    private $_currentConnectionString;

    private $connectionString;
    private $driver;
    private $username;
    private $password;
    private $charset;

    /**
     * This save all query orm use.
     * @var query string
     */
    private $performed_query = [];

    /**
     * Initialize the object or return this object if have value set in attribute $_instance
     * @return Connection
     */
    public function __construct($config)
    {
        $this->connectionString = $config['connectionString'];
        $this->driver = $config['driver'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->charset = $config['charset'];

        self::$_instance = $this;
        return $this;
    }

    /**
     * Initialize the connection
     * @param string $connectionName name on connection
     * @return $this This object from other interator
     * @throws \Exception if the connect name haven't set;
     */
    public function setConnection()
    {
        $this->_currentConnectionString = $this->connectionString;

        try {
            $this->_connection = new \PDO(
                $this->connectionString,
                $this->username,
                $this->password
            );

            $charsetQuery = "set names $this->charset";

            $this->_connection->query($charsetQuery);
            
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    /**
     * Get name on current connection
     * @return string name on current connection or null if don't have
     */
    final public function getCurrentConnectionString()
    {
        return $this->_currentConnectionString;
    }

    /**
     * Initialize the connection
     * @return $this
     * @throws \Exception
     */
    public function connection($connectionString, $username, $password)
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
        return $this->driver;
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
        if(!is_string($schema)) throw new \InvalidArgumentException('The parameter don\'t is an String.');
        if($this->driver == 'mysql') throw new \InvalidArgumentException('This driver not supported schemas.');

        $this->getConnection()->exec("SET search_path TO '$schema';");
        return $this;
    }

}
