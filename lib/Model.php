<?php

namespace MocOrm\Model;

use MocOrm\Connection\ConnectionManager;

abstract class Model extends Query implements \JsonSerializable
{
    /**
     * @var Save current instance
     */
    private static $_instance;

    /**
     * @var Save connection instance
     */
    private $Connection;

    /**
     * @var Save data on object
     */
    private $_data = [];

    /**
     * @var set attributes for update
     */
    private $_newData = [];

    /**
     * @var closure Actived trigger after
     */
    private $triggerAfter = null;

    /**
     * @var closure Actived trigger after
     */
    private $triggerBefore = null;

    /**
     * @var array Save the value to custom query
     */
    protected $_current_custom_query_values = [];

    /**
     * Model constructor.
     * set connection in var and set this instance in var for interator
     */
    public function __construct($object = null)
    {

        try {
            $this->Connection = ConnectionManager::initialize()->current();
            self::$_instance = $this;

            $this->cleanNewData();

            if (!is_null($object)) {
                if (!is_array($object)) throw new \InvalidArgumentException('Accept only array from object');

                $this->_data = $object;
                $this->_newData = $object;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Using if call isset on attributes
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return key_exists($name, $this->_data);
    }

    /**
     * Get value on array data
     * @param String $name
     * @return Value result
     */
    public function __get($name)
    {
        if (strtolower($name) == 'errors') return Error::instance();

        if (!key_exists($name, $this->_data)) throw new \Exception("The attribute $name not found.");

        return $this->_data[$name];
    }

    /**
     * This set values in attribute data and newData
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->_newData[$name] = $value;
        $this->_data[$name] = $value;
    }

    /**
     * @return Save for info in debug only attributes in _data
     */
    public function __debugInfo()
    {
        return $this->_data;
    }

    /**
     * Insert the new data in database not static mode.
     * @param array $attributes Parameters, this is a mirror on database.
     * @return bool|Model|Save False if not save in databse, and this instance if save.
     * @throws \Exception If the parameters isn't one array.
     */
    public function save()
    {
        try {
            $this->verifyConnection();
        } catch (\Exception $e) {
            throw $e;
        }

        $update = false;

        $key = static::$primary_key;
        $_tableName = static::$table_name;

        $repeat = substr(str_repeat(' ?, ', count($this->_newData)), 0, -2);

        /**
         * Edit case have primary key value
         */
        if (!empty($this->_data[$key])) {
            $sql = 'UPDATE ' . $_tableName . ' SET ';

            if (count($this->_newData) <= 0){
                Error::create('Don\'t have alter data.', 1,'InvalidArgumentException');
                return false;
            }

            $sql .= implode(" = ?, ", array_keys($this->_newData)) . ' = ?';
            $sql .= " WHERE $key = ? ";

            $this->_newData[] = $this->{$key};

            $update = true;
        } else {
            /**
             * Insert case don't have primary key
             */
            $sql = 'INSERT INTO ' . $_tableName;
            $sql .= ' (' . implode(', ', array_keys($this->_data)) . ') ';
            $sql .= " VALUES ";
            $sql .= " ($repeat); ";
        }

        if (is_callable($this->triggerBefore)) ($this->triggerBefore)();

        $start = microtime(true);
        $insert = $this->Connection->getConnection()->prepare($sql);
        $this->burnError($insert);

        $this->_newData = array_map(function ($data){
            if(is_bool($data) and $data === false)  $data = 0;

            return $data;
        }, $this->_newData);

        $insert->execute(array_values($this->_newData));
        $end = microtime(true);
        $this->burnError($insert);

        $this->Connection->setPerformedQuery($insert->queryString, round(($end - $start), 5));

        if (is_callable($this->triggerAfter)) ($this->triggerAfter)();

        if ($update) {
            $this->burnError($insert);

            $this->cleanNewData();
            return true;
        }

        $this->_data[$key] = $this->Connection->getConnection()->lastInsertId();

        if ($insert->rowCount() == 0) {
            throw new \Exception($insert->errorInfo()[2], $insert->errorInfo()[1]);
        }

        return true;
    }

    private function burnError($statment){
        if (!is_null($statment->errorInfo()[1])) throw new \Exception($statment->errorInfo()[2], $statment->errorInfo()[1]);
    }

    /**
     * Delete the register
     * @return bool
     */
    public function delete()
    {
        try {
            $this->verifyConnection();
        } catch (Exception $e) {
            Throw new \Exception($e->getMessage());
        }

        if (!isset(static::$primary_key)) throw new \Exception('Primary key don\'t set');
        if (!is_numeric($this->{static::$primary_key})) throw new \Exception('Primary key value don\'t is valid');

        $sql = ' DELETE FROM ' . static::$table_name;
        $sql .= ' WHERE ' . static::$primary_key . ' = ? ';

        $instance = self::$_instance;

        if (is_callable($this->triggerBefore)) ($this->triggerBefore)();;

        $start = microtime(true);

        $insert = $instance->Connection->getConnection()->prepare($sql);
        $insert->execute([$this->{static::$primary_key}]);

        $end = microtime(true);

        $instance->Connection->setPerformedQuery($insert->queryString, round(($end - $start), 5));

        if (is_callable($this->triggerAfter)) ($this->triggerAfter)();

        if ($insert->rowCount() > 0) return true;
        else return false;
    }

    /**
     * JsonSerializable Interface.
     */
    public function jsonSerialize()
    {
        return $this->_data;
    }

    /**
     * Get all data on database needed table name in Model
     * @return array all data in format Object
     * @throws \Exception Don't set table name in model.
     */
    public static function all()
    {
        if (!isset(static::$table_name)) throw new \Exception('Don\'t set table name in model.');

        $currentTable = static::$table_name;

        self::instance();

        $instance = self::$_instance;
        try {
            self::$_instance->verifyConnection();
        } catch (Exception $e) {
            Throw new \Exception($e->getMessage());
        }

        $sql = "SELECT * FROM $currentTable ";

        $start = microtime(true);
        $consulta = $instance->Connection->getConnection()->prepare($sql);
        $consulta->execute();
        $objetos = $consulta->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        $end = microtime(true);

        $instance->Connection->setPerformedQuery($consulta->queryString, round(($end - $start), 5));

        return $instance->_data = $objetos;
    }

    /**
     * Execute one procedure
     * @param $procedureName Name of the procedure
     * @param array $param Parameters needed in procedure
     * @return mixed Result on procedure
     * @throws \Exception Case procedureName don't is string or param not is array
     */
    public static function procedure($procedureName, $param = [])
    {
        self::instance();

        if (!is_string($procedureName)) throw new \Exception("Procedure name is invalid.");
        if (!is_array($param)) throw new \Exception("Tipo de parâmetros inválidos.");

        $currentTable = static::$table_name;

        $instance = self::$_instance;

        try {
            self::$_instance->verifyConnection();
        } catch (\Exception $e) {
            Throw new \Exception($e->getMessage());
        }

        $repeat = substr(str_repeat(' ?, ', count($param)), 0, -2);

        $drivers = $instance->Connection->getDriver();

        switch ($drivers) {
            case 'mysql':
                $sql = "call $currentTable ($repeat)";
                break;
            case 'pgsql':
                $sql = "select $procedureName($repeat)";
                break;
            default:
                throw new \Exception('Don\'t exists implementation on this driver.');
                break;
        }

        $start = microtime(true);
        $consulta = $instance->Connection->getConnection()->prepare($sql);
        $consulta->execute($param);
        $objetos = $consulta->fetchAll(\PDO::FETCH_CLASS, get_class($instance));
        $end = microtime(true);

        $instance->Connection->setPerformedQuery($sql, round(($end - $start), 5));

        return $instance->_data = $objetos;
    }

    /**
     * Insert the new data in database static mode.
     * @param array $attributes Parameters, this is a mirror on database.
     * @return bool|Model|Save False if not save in databse, and this instance if save.
     * @throws \Exception If the parameters isn't one array.
     */
    public static function create($attributes = [])
    {
        self::instance();

        self::$_instance->_data = [];

        if (!is_array($attributes)) throw new \Exception("Invalid parameter type.");

        $repeat = substr(str_repeat(' ?, ', count($attributes)), 0, -2);

        $sql = 'INSERT INTO ' . static::$table_name;
        $sql .= ' (' . implode(', ', array_keys($attributes)) . ') ';
        $sql .= " VALUES ";
        $sql .= " ($repeat); ";

        $instance = self::$_instance;

        try {
            self::$_instance->verifyConnection();
        } catch (Exception $e) {
            Throw new \Exception($e->getMessage());
        }

        if (is_callable(self::$_instance->triggerBefore)) self::$_instance->triggerBefore();

        $start = microtime(true);

        $insert = $instance->Connection->getConnection()->prepare($sql);
        $insert->execute(array_values($attributes));

        $end = microtime(true);

        $instance->Connection->setPerformedQuery($insert->queryString, round(($end - $start), 5));

        if (is_callable(self::$_instance->triggerAfter)) self::$_instance->triggerAfter();

        $instance->_data = $attributes;
        $instance->_data[static::$primary_key] = $instance->Connection->getConnection()->lastInsertId();

        if ($instance->Connection->getConnection()->lastInsertId() == '-1' || $instance->Connection->getConnection()->lastInsertId() == 0) {
            return false;
        } else {
            return $instance;
        }
    }

    /**
     * Find the data on primary key
     * @param Array or Integer $parameters Value on primary key
     * @return mixed Data or false
     * @throws \Exception if Parameter is invalid
     */
    public static function find($parameters = null)
    {
        self::instance();

        try {
            self::$_instance->verifyConnection();
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if (!is_array($parameters) and !is_numeric($parameters)) throw new \Exception('Invalid parameter type on model ' . get_called_class() . '.');

        self::$_instance->_current_custom_query[] = 'SELECT * FROM ' . static::$table_name . ' ';

        switch ($parameters) {
            case is_numeric($parameters):
                if (!isset(static::$primary_key)) throw new \Exception("Invalid parameter type.");

                self::$_instance->_current_custom_query_values[] = $parameters;
                self::$_instance->_current_custom_query[] = ' WHERE ' . static::$primary_key . ' = ?';

                break;
            case is_array($parameters):
                break;
            default:
                throw new Exception('Invalid parameter type.');
                break;
        }

        $done = self::$_instance->done();

        return count($done) > 0 ? $done[0] : null;
    }

    /**
     * Init the get data dynamic, the last method to use this is done() for execute
     * @param String $colunm This is all colunm from select
     * @return Model|Save A model if success and false if don't have success
     * @throws \Exception If colunm isn't String
     */
    final public static function select($colunm = '*')
    {
        self::instance();

        try {
            self::$_instance->verifyConnection();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if (!is_string($colunm)) throw new \Exception("Invalid parameter type.");

        self::$_instance->_current_custom_query[] = "SELECT $colunm FROM " . static::$table_name . ' ';

        if (!isset(static::$primary_key)) throw new \Exception("Invalid parameter type.");

        return self::$_instance;
    }

    /**
     * Get current connection name
     * @return string Connection name
     * @throws \Exception
     */
    protected function getCurrentConnectionName()
    {
        try {
            return $this->Connection->getCurrentConnectionName();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get all connection string.
     * @return array Return all configs defined
     * @throws \Exception
     */
    protected function getConfigs()
    {
        try {
            return $this->Connection->getConfigs();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Define the current connection on name
     * @param $connectionName This is connection name
     * @return $this This from other implementations
     * @throws \Exception
     */
    protected function setConnection($connectionName)
    {
        try {
            $this->Connection->setConnection($connectionName);
            return $this;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Execute the query
     * @param $query The query for execute
     * @return $objects Results on query executed
     */
    protected function query($query, $param = [])
    {
        $this->_data = [];

        $select = trim($query);
        $select = strtolower($select);

        $match = preg_match('/^select|return|^with\srecursive/', $select);

        try {
            $this->verifyConnection();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if (!is_array($param)) throw new \Exception('Tipo de parâmetro inválido.');

        $start = microtime(true);

        $consulta = $this->Connection->getConnection()->prepare($query);
        $this->burnError($consulta);

        $consulta->execute($param);
        $this->burnError($consulta);

        if (!$consulta) {
            $this->_data = false;
            return $this;
        }

        $end = microtime(true);

        if ($match) {
            $this->_data = $objetos = $consulta->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        } else {
            $objetos = $consulta->rowCount();
        }

        $this->Connection->setPerformedQuery($query, round(($end - $start), 5));

        $this->burnError($consulta);

        return $objetos;
    }

    /**
     * Execute the query on static method
     * @param $query The query for execute
     * @return $objects Results on query executed
     */
    public static function sql($query, $param = [])
    {
        self::instance();

        try {
            self::$_instance->verifyConnection();
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if (!is_array($param)) throw new \Exception('Invalid parameter type.');

        self::$_instance->_current_custom_query_values = $param;

        $objetos = self::$_instance->query($query, self::$_instance->_current_custom_query_values);

        self::$_instance->_current_custom_query_values = [];

        self::$_instance->cleanNewData();

        return $objetos;
    }

    /**
     * Get the last execute query from ORM
     * @return string on last query
     */
    final protected function getLastQuery()
    {
        return $this->Connection->getLastPerformedQuery();
    }

    /**
     * Get all execute query from ORM
     * @return all query executed
     */
    final protected function getAllPerfomedQuery()
    {
        return $this->Connection->getPerformedQuery();
    }

    /**
     * Execute an closure after insert, update or delete.
     * @param $closure
     * @throws \Exception If colunm isn't a closure
     * @return $this
     */
    final protected function setTriggerAfter($closure = null)
    {
        if (!is_callable($closure)) throw new Exception('The parameter don\'t is an closure.');

        $this->triggerAfter = $closure;

        return $this;
    }

    /**
     * Execute an closure before insert, update or delete.
     * @param $closure
     * @throws \Exception If colunm isn't a closure
     * @return $this
     */
    final protected function setTriggerBefore($closure = null)
    {
        if (!is_callable($closure)) throw new Exception('The parameter don\'t is an closure.');

        $this->triggerBefore = $closure;

        return $this;
    }

    /**
     * Change schema on postgres
     * @param $schema schema name
     * @return $this
     */
    final protected function changeSchema($schema)
    {
        if (!is_string($schema)) throw new \Exception('The parameter don\'t is an String.');

        $this->Connection->changeSchema($schema);
        return $this;
    }

    /**
     * Clean data
     * @return $this
     */
    final protected function cleanNewData()
    {
        $this->_newData = [];
        return $this;
    }

    protected function getData()
    {
        return $this->_data;
    }

    /**
     * Auxiliar method for current instance is set
     * @return current class
     */
    final private static function instance()
    {
        $calledClass = get_called_class();

        self::$_instance = new $calledClass();

        self::$_instance->Connection = ConnectionManager::initialize()->current();

        return self::$_instance;
    }

    final private function verifyConnection()
    {
        if (is_null($this->Connection->getCurrentConnectionString())) throw new \Exception('Not set connection.');
    }
}