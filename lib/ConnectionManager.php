<?php

namespace orm\connection;

class ConnectionManager
{
    /**
     * @Connection object is a singleton of Connection
     */
    private $Connection;

    /**
     * @Connection array is all connections opened
     */
    private $connections = [];

    /**
     * @currentConnection object is the current connection
     */
    private $currentConnection;

    /**
     * @_instance object this instance
     */
    private static $_instance;

    /**
     * @currentConnectionName string of the current connection name
     */
    private $_currentConnectionName;

    /**
     * Initialize the object or return this object if have value set in attribute $_instance
     * @param \Closure $connection
     * @return ConnectionManager
     */
    public static function initialize($connection = null)
    {

        if (!self::$_instance) self::$_instance = new ConnectionManager();

        if (is_callable($connection)) self::$_instance->Connection = $connection(new Connection);

        return self::$_instance;
    }

    /**
     * @param $connectionName String
     * @return $this
     * @throws \Exception if isn't an string
     * @throws \Exception if isn't set
     */
    public static function open($connectionName)
    {
        if (!is_string($connectionName)) throw new \Exception("Invalid connection name.");
        if (!self::$_instance->hasConnection($connectionName)) throw new \Exception("The connection name $connectionName is not set.");
        if (self::$_instance->hasOpen($connectionName)) throw new \Exception('This connection is actived.');

        self::$_instance->_currentConnectionName = $connectionName;
        self::$_instance->connections[$connectionName] = self::$_instance->Connection->setConnection($connectionName);

        self::$_instance->currentConnection = self::$_instance->connections[$connectionName];

        return self::$_instance;
    }

    /**
     * This get the current connection
     * If not exists the current connection this use last setting
     * @return  Connection
     * @throws \Exception if not exists connections set
     */
    public function current()
    {
        if(empty($this->currentConnection)){
            $configs = $this->Connection->getConfigs();

            if(count($configs) == 0) throw new \Exception('Not have connection');

            $name = @end(array_keys($configs));

            self::open($name);
        }

        return $this->currentConnection;
    }

    /**
     * Change current connection but this needed is set
     * @param $connectionName
     * @return mixed
     * @throws \Exception if connection name isn't string
     * @throws \Exception if connection name isn't set
     * @throws \Exception if connection not actived
     */
    public static function change($connectionName)
    {
        if (!is_string($connectionName)) throw new \Exception("Invalid connection name.");
        if (!self::$_instance->hasConnection($connectionName)) throw new \Exception("The connection name $connectionName is not set.");
        if (!self::$_instance->hasOpen($connectionName)) throw new \Exception('This connection isn\'t actived.');

        self::$_instance->_currentConnectionName = $connectionName;

        self::$_instance->currentConnection = self::$_instance->connections[$connectionName];

        return self::$_instance;
    }

    /**
     * All connections opened
     * @return array All connections
     * @throws \Exception This instance isnt't initialized
     */
    public function getAllActive()
    {
        if (!$this->hasInstance()) throw new \Exception('This instance isnt\'t initialized');

        return $this->connections;
    }

    /**
     * Verify if connection has open
     * @param $connectionName
     * @return bool
     */
    private function hasOpen($connectionName)
    {
        if (array_key_exists($connectionName, $this->connections)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verify if this is initialized
     * @return bool
     */
    private function hasInstance()
    {
        return !self::$_instance ? false : true;
    }

    /**
     * Verify if has connection defined
     * @param $connectionName
     * @return bool
     */
    private function hasConnection($connectionName)
    {
        if (array_key_exists($connectionName, $this->Connection->getConfigs())) {
            return true;
        } else {
            return false;
        }
    }
}