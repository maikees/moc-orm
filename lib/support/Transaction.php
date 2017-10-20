<?php

namespace MocOrm\Support;

use MocOrm\Connection\ConnectionManager;

/**
 * Implementation.
 */
class Transaction
{
    /**
     * Current transaction's database.
     * @var string
     */
    protected $db;

    /**
     * Transaction result.
     * @var array
     */
    protected $results;

    /**
     * SQL Query error.
     * @var mixed
     */
    protected $error;


    /**
     * Transacion
     * Responsible to holds transaction behave.
     * @param mixed $closure
     */
    public function __construct($closure = null)
    {
        if (!is_callable($closure)) {
            throw new \Exception("Transaction must have a callable as parameter.");

        } else {
            $connection = $this->getConnection();

            try {
                $connection->beginTransaction();

                $this->results = $closure($connection);

                if ($this->results === false) {
                    $connection->rollbackTransaction();
                } else {
                    $connection->commitTransaction();
                }

            } catch (\Exception $e) {
                $connection->rollbackTransaction();
                $this->error = $e;
            }

        }
        return $this;
    }

    /**
     * Gets current connection.
     * @param  string $name
     * @return \Connection
     */
    public function getConnection($name = null)
    {
        return ConnectionManager::initialize()->current();
    }

    /**
     * Gets query results.
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Gets transacion's error.
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Checks if transacion has error.
     * @return boolean
     */
    public function hasError()
    {
        return isset($this->error);
    }
}
