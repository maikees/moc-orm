<?php

namespace MocOrm\Tests\Connection;

use MocOrm\Connection\ConnectionManager;
// use MocOrm\Tests\Connection\ConnectionManagerHelper;

final class ConnectionManagerTest extends \PHPUnit\Framework\TestCase
{
    public function testInitialize()
    {
        $got = ConnectionManager::initialize();
        $this->assertInstanceOf(ConnectionManager::class, $got);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage No connections are available.
     */
    public function testCurrentWithoutConnections()
    {
        // ConnectionManagerHelper::clearInstance();
        $connectionManager = ConnectionManager::initialize();
        $connectionManager->current();
    }

    public function testGetAllActive()
    {
        // ConnectionManagerHelper::clearInstance();
        $connectionManager = ConnectionManager::initialize();
        $got = $connectionManager->getAllActive();
        $this->assertEquals(0, count($got));
    }

    public function testInitializeWithConfigCallable()
    {
        $got = ConnectionManager::initialize(
            function ($connection) {
                $connection->addConfig('pgsql', 'root', 'password', 'localhost', 'local_postgres', 'local', 3306);
                $connection->addConfig('mysql', 'root', 'password', 'localhost', 'local_mysql', 'test', 3306);
                return $connection;
            }
        );
        $this->assertInstanceOf(ConnectionManager::class, $got);
        $configs = $got->getConfigs();
        $this->assertEquals(2, count($configs));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid connection name.
     */
    public function testOpenWithInvalidConnectionName()
    {
        $connectionManager = ConnectionManager::initialize();
        $connectionManager->open('');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage The connection name CONNECTION_NAME is not set.
     */
    public function testOpenWithConnectionNameDoesntSetted()
    {
        $connectionManager = ConnectionManager::initialize();
        $connectionManager->open('CONNECTION_NAME');
    }
}
