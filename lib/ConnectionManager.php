<?php

namespace MocOrm\Connection;

class ConnectionManager
{
    /**
     * @Connection object is a of Connection
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
    protected static $_instance;

    /**
     * @currentConnectionName string of the current connection name
     */
    private $_currentConnectionName;

    /**
     * @Config This is a singleton object for save all configs
     */
    private $Config;

    /**
     * Initialize the object or return this object if have value set in attribute $_instance
     * @param \Closure $config
     * @return ConnectionManager
     */
    public static function initialize($config = null)
    {
        if (!self::$_instance) self::$_instance = new ConnectionManager();

        if (is_callable($config)) {

            $config($_config = new Config());

            self::$_instance->Config = $_config;
        }

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
        $instance = self::$_instance;

        if (!is_string($connectionName) || empty($connectionName)) throw new \Exception("Invalid connection name.");
        if (!$instance->hasConnection($connectionName)) throw new \Exception("The connection name $connectionName is not set.");
        if ($instance->hasOpen($connectionName)) throw new \Exception('This connection is actived.');

        $instance->_currentConnectionName = $connectionName;

        $configs = $instance->Config->getConnection($connectionName);

        $instance->Connection = new Connection($configs);

        $instance->connections[$connectionName] = $instance->Connection->setConnection();

        $instance->currentConnection = $instance->connections[$connectionName];

        if ($instance->currentConnection->getDriver() == 'pgsql') {
            $instance->currentConnection->changeSchema($instance->currentConnection->getSchema());
        }

        return $instance;
    }

    /**
     * This get the current connection
     * If not exists the current connection this use last setting
     * @return  Connection
     * @throws \Exception if not exists connections set
     */
    public function current()
    {
        if (empty($this->currentConnection)) {
            $configs = ($this->Config) ? $this->Config->getConfigs() : null;

            if (count($configs) == 0) throw new \Exception('No connections are available.');

            $default = $this->Config->getDefault();

            $name = is_null($default) ?
                @end(array_keys($configs)) :
                $this->Config->getDefault();

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
    public static function change($connectionName, $open = false)
    {
        $instance = self::initialize();

        if (!is_string($connectionName)) throw new \Exception("Invalid connection name.");

        if (!$instance->hasConnection($connectionName)) throw new \Exception("The connection name $connectionName is not set.");

        if (!$instance->hasOpen($connectionName)) {
            if ($open) {
                try {
                    self::open($connectionName);
                } catch (\Exception $e) {
                    throw $e;
                }
            } else {

                throw new \Exception("This connection isn't actived.");
            }
        }

        $instance->_currentConnectionName = $connectionName;

        $instance->currentConnection = $instance->connections[$connectionName];

        $instance->Connection = $instance->connections[$connectionName];

        return $instance;
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
     * Get all connection string
     * @return array if have connection but not have this method return null
     */
    final public function getConfigs()
    {
        return $this->Config->getConfigs();
    }

    /**
     * Verify if connection has open
     * @param $connectionName
     * @return bool
     */
    private function hasOpen($connectionName)
    {
        return array_key_exists($connectionName, $this->connections);
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
        return array_key_exists(
            $connectionName,
            $this->Config->getConfigs()
        );
    }
}
