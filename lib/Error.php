<?php

namespace mocorm\model;

class Error
{
    private static $_instance;

    const ACCEPTED_ERROR = [
        'Exception',
        'InvalidArgumentException',
    ];

    private $errors = [];
    private $burst;

    public function __construct()
    {
        return $this;
    }

    public static function create($message, $code, $type = 'Exception')
    {
        $instance = self::instance();

        if($instance->burst) $instance->burst($message, $code, $type);

        $errors['message'] = $message;
        $errors['code'] = $code;
        $errors['type'] = $type;

        $instance->errors[] = $errors;

        return $instance;
    }

    public static function instance()
    {
        if (!self::$_instance) self::$_instance = new self(); return self::$_instance;
    }

    public function getAll()
    {
        return $this->errors;
    }

    public function getLast()
    {
        return end($this->errors);
    }

    public function getFirst()
    {
        return current($this->errors);
    }

    private function burst($message, $code, $type){
//        if(!array_search($type, ERRORS)) throw new \InvalidArgumentException('Type of burst not found.');
        $type = '\\'.$type;
        throw new $type($message, $code);
    }
}