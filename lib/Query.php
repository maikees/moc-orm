<?php

namespace orm\model;

abstract class Query{
    /**
     * @var array Save the current query for search on database
     */
    protected $_current_custom_query = [];

    /**
     * @var array Values on currents join
     */
    protected $_joins = [];

    /**
     * @var string Values on currents group in query
     */
    protected $_group;

    /**
     * This function is a closed on 'SELECT' and execute all parameter
     * @return $objects Return all data on parameters before sending
     */
    final public function done()
    {
        $query = $this->queryBuilder();

        $objetos = $this->query($query, $this->_current_custom_query_values);

        $this->_current_custom_query_values = [];

        $this->_data = $objetos;

        $this->cleanNewData();

        return $objetos;
    }

    /**
     * This function is a mirror of 'where' on database
     * @param null $parameter The colunm for compare
     * @param null $value Value to compare
     * @return $this This object for others interation
     * @throws \Exception Case types on parameter is invalid or not set.
     */
    final public function where($parameter = null, $value = null)
    {
        if (!is_string($parameter)) throw new \Exception('Invalid parameter type.');
        if (!isset($value)) throw new \Exception('This value not set.');

        $this->_current_custom_query[] = " WHERE $parameter = ? ";
        $this->_current_custom_query_values[] = $value;

        return $this;
    }

    /**
     * This function is a mirror of 'AND' on database
     * @param null $parameter The colunm for compare
     * @param null $value Value to compare
     * @return $this This object for others interation
     * @throws \Exception Case types on parameter is invalid or not set.
     */
    final public function and ($parameter = null, $value = null)
    {
        if (!is_string($parameter)) throw new \Exception('Invalid parameter type.');
        if (!isset($value)) throw new \Exception('This value not set.');

        $this->_current_custom_query[] = " AND $parameter = ? ";
        $this->_current_custom_query_values[] = $value;

        return $this;
    }

    /**
     * This function is a mirror of 'OR' on database
     * @param null $parameter The colunm for compare
     * @param null $value Value to compare
     * @return $this This object for others interation
     * @throws \Exception Case types on parameter is invalid or not set.
     */
    final public function or ($parameter = null, $value = null)
    {
        if (!is_string($parameter)) throw new \Exception('Invalid parameter type.');
        if (!isset($value)) throw new \Exception('This value not set.');

        $this->_current_custom_query[] = " OR $parameter = ? ";
        $this->_current_custom_query_values[] = $value;

        return $this;
    }

    /**
     * This function is a mirror of 'Order By' on database
     * @param null $parameter The colunm for compare
     * @param null $value Value to compare
     * @return $this This object for others interation
     * @throws \Exception Case types on parameter is invalid or not set.
     */
    final public function orderBy($parameter = null, $value = null)
    {
        if (!is_string($parameter)) throw new \Exception('Invalid parameter type.');
        if (!isset($value)) throw new \Exception('This value not set.');
        if (!is_string($value)) throw new \Exception('Don\'t accepted this type on value.');
        if ($value != 'ASC' AND $value != 'DESC') throw new \Exception('This value not found.');

        $this->_current_custom_query[] = " ORDER BY $parameter $value";

        return $this;
    }

    /**
     * This function is a mirror of 'LEFT JOIN' on database
     * @param string $join
     * @return $this
     */
    final public function leftJoin($join = '')
    {
        $this->_joins[] = ' LEFT JOIN ' . $join;
        return $this;
    }

    /**
     * This function is a mirror of 'LEFT JOIN' on database
     * @param string $join
     * @return $this
     */
    final public function custom($partialQuery)
    {
        if (!is_string($partialQuery)) throw new \Exception('Invalid parameter type.');

        $this->_current_custom_query[] = $partialQuery;

        return $this;
    }

    /**
     * This function is a mirror of 'Right JOIN' on database
     * @param string $join
     * @return $this
     */
    final public function rightJoin($join = '')
    {
        $this->_joins[] = ' RIGHT JOIN ' . $join;
        return $this;
    }

    /**
     * This function is a mirror of 'INNER JOIN' on database
     * @param string $join
     * @return $this
     */
    final public function innerJoin($join = '')
    {
        $this->_joins[] = ' INNER JOIN ' . $join;
        return $this;
    }

    /**
     * This function is a mirror of 'FULL OUTER JOIN' on database
     * @param string $join
     * @return $this
     */
    final public function fullOuterJoin($join = '')
    {
        $this->_joins[] = ' FULL OUTER JOIN ' . $join;
        return $this;
    }

    /**
     * This function is a mirror of 'Group By' on database
     * @param $colunm
     * @return $this
     * @throws \Exception
     */
    final public function groupBy($colunm)
    {
        if (!is_string($colunm)) throw new \Exception('The colunm isn\'t an string.');

        $this->_group = " GROUP BY $colunm ";

        return $this;
    }

    /**
     * Builder query
     * @return string The query mounted for using.
     */
    final protected function queryBuilder()
    {
        $sql = array_shift($this->_current_custom_query);

        $sql .= implode('', $this->_joins);

        $last = array_pop($this->_current_custom_query);

        $sql .= implode('', $this->_current_custom_query);

        if (substr_count($last, 'ORDER BY') > 0) {
            $sql .= isset($this->_group) ? $this->_group : '';
            $sql .= $last;
        } else {
            $sql .= $last;
            $sql .= isset($this->_group) ? $this->_group : '';
        }

        $this->_current_custom_query = [];

        return $sql;
    }
}