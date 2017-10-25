<?php

namespace MocOrm\Connection;

use Mockery\Exception;

class Config
{
    /**
     * @connectionString array of string This attribute is internal for save the connection string
     */
    private $_connectionString = [];

    /**
     * @username array of string This attribute is internal for save the connection usernames
     */
    private $_username = [];

    /**
     * @password array of string This attribute is internal for save the connection passwords
     */
    private $_password = [];

    /**
     * List of drivers sets on connections
     * @var array $_driver
     */
    private $_driver = [];

    /**
     * List of drivers sets on connections
     * @var $_driver
     */
    private $_charset = [];

    /**
     * List of schemas sets on connections
     * @var array $_schema
     */
    private $_schema = [];

    /**
     * Constant to define set accepted drivers.
     * @var array DRIVERS all accepted drivers
     */
    const DRIVERS = [
        "mysql",
        "pgsql",
    ];

    private $default = null;

    /**
     * @var boolean $appLogger Configure if application save log or no
     */
    private $appLogger;

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
    public function addConfig(
        $driver = 'mysql',
        $username = "root",
        $password = null,
        $host = "localhost",
        $database = null,
        $connectionName = null,
        $port = null,
        $charset = 'utf8',
        $defaultSchema = null)
    {
        #Begin: Verify if all parameters send is valid.
        if (!is_string($driver) || !in_array($driver, self::DRIVERS)) throw new \Exception("The driver $driver don't supported.");
        if (!is_string($username) || empty($username)) throw new \Exception("Invalid username.");
        if (!is_string($password) || empty($password)) throw new \Exception("Invalid password.");
        if (!is_string($host) || empty($host)) throw new \Exception("Invalid host.");
        if (!is_string($database) || empty($database)) throw new \Exception("Invalid database name.");
        $this->validatesConnectionName($connectionName);

        $port = is_null($port) ? '' : (int)$port;
        if (!is_null($port) && !is_int($port)) throw new \Exception("Invalid port format.");
        #Constructor of the connection string
        $this->_connectionString[$connectionName] = "$driver:host=$host;dbname=$database;port=$port;";
        $this->_username[$connectionName] = $username;
        $this->_password[$connectionName] = $password;
        $this->_driver[$connectionName] = $driver;
        $this->_charset[$connectionName] = $charset;
        $this->_schema[$connectionName] = $defaultSchema;

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
     * Set the current connection on connection name.
     * @param string $connectionName name on connection
     * @return $this This object from other interator
     * @throws \Exception if the connect name haven't set;
     */
    public function setDefault($connectionName)
    {
        $this->default = $connectionName;
        return $this;
    }

    /**
     * Set the current connection on connection name.
     * @param string $connectionName name on connection
     * @return $this This object from other interator
     * @throws \Exception if the connect name haven't set;
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set the current connection on connection name.
     * @param string $connectionName name on connection
     * @return $this This object from other interator
     * @throws \Exception if the connect name haven't set;
     */
    public function getConnection($connectionName)
    {
        $this->validatesConnectionName($connectionName);

        if (array_key_exists($connectionName, $this->_connectionString)) {
            return [
                'connectionString' => $this->_connectionString[$connectionName],
                'driver' => $this->_driver[$connectionName],
                'username' => $this->_username[$connectionName],
                'password' => $this->_password[$connectionName],
                'charset' => $this->_charset[$connectionName],
                'schema' => $this->_schema[$connectionName]];
        } else {
            throw new \Exception("The connection name $connectionName is not set.");
        }

        return $this;
    }

    private function validatesConnectionName($connectionName)
    {
        if (!is_string($connectionName) || empty($connectionName)) {
            throw new \Exception("Invalid connection name.");
        }
    }

    /**
     * @return mixed
     */
    public function getConnectionString($connectionName)
    {
        return $this->_connectionString[$connectionName];
    }

    /**
     * @param mixed $connectionString
     */
    public function setSettings($connectionName, $connectionSettings)
    {
        if (!is_array($connectionSettings)) throw new \Exception('Invalid format connectionSettings');
        if (empty($this->_driver[$connectionName])) throw new \Exception('Driver not set.');

        $this->validatesConnectionName($connectionName);

        $connectionSettings = (object)$connectionSettings;

        $this->_connectionString[$connectionName] = $this->_driver[$connectionName].":host=$connectionSettings->host;dbname=$connectionSettings->database;port=$connectionSettings->port;";

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername($connectionName)
    {
        return $this->_username[$connectionName];
    }

    /**
     * @param mixed $username
     */
    public function setUsername($connectionName, $username = "root")
    {
        $this->validatesConnectionName($connectionName);

        $this->_username[$connectionName] = $username;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword($connectionName)
    {
        return $this->_password[$connectionName];
    }

    /**
     * @param mixed $password
     */
    public function setPassword($connectionName, $password)
    {
        $this->validatesConnectionName($connectionName);

        $this->_password[$connectionName] = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDriver($connectionName)
    {
        return $this->_driver[$connectionName];
    }

    /**
     * @param mixed $driver
     */
    public function setDriver($connectionName, $driver = 'mysql')
    {
        $this->validatesConnectionName($connectionName);

        $this->_driver[$connectionName] = $driver;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCharset($connectionName)
    {
        return $this->_charset[$connectionName];
    }

    /**
     * @param mixed $charset
     */
    public function setCharset($connectionName, $charset = 'utf8')
    {
        $this->validatesConnectionName($connectionName);

        $this->_charset[$connectionName] = $charset;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSchema($connectionName)
    {
        return $this->_schema[$connectionName];
    }

    /**
     * @param mixed $schema
     */
    public function setSchema($connectionName, $schema)
    {
        $this->validatesConnectionName($connectionName);

        $this->_schema[$connectionName] = $schema;

        return $this;
    }

    /**
     * @return $this
     */
    public function appEnableLogger() {
        $this->appLogger = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function appDisableLogger() {
        $this->appLogger = false;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getAppLogger() {
        return $this->appLogger;
    }
}
