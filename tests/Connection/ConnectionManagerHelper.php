<?php

namespace MocOrm\Tests\Connection;

use MocOrm\Connection\ConnectionManager;

class ConnectionManagerHelper extends ConnectionManager
{
    public static function clearInstance()
    {
        self::$_instance = null;
    }
}
