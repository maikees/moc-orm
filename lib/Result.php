<?php
/**
 * User: Maike Oliveira
 * Date: 24/10/17
 * Time: 09:03
 */

namespace MocOrm\Model;

use MocOrm\Support\Log;

class Result
{
    /**
     * @var $last_function Last function execute on ORM
     */
    private $last_function;
    /**
     * @var $results Last result orm
     */
    private $results;
    /**
     * @var Log
     */
    private $Log;

    public function __construct()
    {
//        $this->Log = new Log;
    }

    /**
     * @return mixed
     */
    public function getLastFunction()
    {
        return $this->last_function;
    }

    /**
     * @param mixed $last_function
     */
    public function setLastFunction($last_function)
    {
        $this->last_function = $last_function;
    }

    /**
     * @return mixed
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param mixed $results
     */
    public function setResults($results)
    {
        $this->results = $results;
    }
}