<?php


namespace MocOrm\Usage;

use MocOrm\Model\Model;

/**
 *  1. Extends your model to class Model, using namespace orm\model\Model
 */
class UsageModel extends Model
{
    /**
     *  2. Set the static attribute $primary_key in your model
     *      - This is an string
     *      @var string
     */
    static $table_name = 'chave_composta';
    /**
     *  3. Set the static attribute $table_name in your model
     *      @var This is an string
     * @var string
     */
    static $primary_key = 'id';

    /**
     * Query Example
     *
     * 5. For get all data using not static method query in model, but this needed the method is instantiated in model
     *      @var query
     *      @return Array with object if exists the data
     *      @return Array haven't data
     */
    public function useQuery(){
        return $this->query('SELECT * FROM '.static::$table_name);
    }


}
