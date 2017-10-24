<?php

namespace MocOrm\Tests\Connection;

use MocOrm\Connection\Config;

final class ConfigTest extends \PHPUnit\Framework\TestCase
{
    protected $driver = 'pgsql';
    protected $username = "anakin";
    protected $password = 'padmé';
    protected $host = "localhost";
    protected $database = 'postgres';
    protected $connectionName = 'postgres';
    protected $port = 5432;
    protected $charset = 'utf8';
    protected $defaultSchema = 'schema';

    /**
     * @expectedException Exception
     * @expectedExceptionMessage The driver invalid_driver don't supported.
     */
    public function testDriverDontSupported()
    {
        (new Config)->addConfig('invalid_driver');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid username.
     */
    public function testInvalidUsername()
    {
        (new Config)->addConfig($this->driver, null);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid password.
     */
    public function testInvalidPassword()
    {
        (new Config)->addConfig($this->driver, $this->username);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid host.
     */
    public function testInvalidHost()
    {
        (new Config)->addConfig($this->driver, $this->username, $this->password, '');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid database name.
     */
    public function testInvalidDatabaseName()
    {
        (new Config)->addConfig($this->driver, $this->username, $this->password, $this->host);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid connection name.
     */
    public function testInvalidConnectionName()
    {
        (new Config)->addConfig($this->driver, $this->username, $this->password, $this->host, $this->database);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid port format.
     */
    public function testInvalidPort()
    {
        (new Config)->addConfig($this->driver, $this->username, $this->password, $this->host, $this->database, $this->connectionName);
    }

    public function testAddConfig()
    {
        $config = $this->getConfigFilled();
        $this->assertInstanceOf(Config::class, $config);
    }

    public function testGetConfig()
    {
        $config = (new Config)->getConfigs();
        $this->assertEmpty($config);
        $this->assertEquals([], $config);

        $config = $this->getConfigFilled();
        $configs = $config->getConfigs();
        $this->assertNotEmpty($configs);
        $first_key = key($configs);
        $this->assertEquals('postgres', $first_key);
        $this->assertEquals('pgsql:host=localhost;dbname=postgres;port=5432;', $configs[$first_key]);
    }

    public function testDefault()
    {
        $config = $this->getConfigFilled();
        $got = $config->setDefault('newConnectionName');
        $this->assertInstanceOf(Config::class, $got);

        $default = $config->getDefault();
        $this->assertEquals('newConnectionName', $default);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid connection name.
     */
    public function testGetInvalidConnection()
    {
        $config = $this->getConfigFilled();
        $config->getConnection('');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage The connection name nonexistent_connection is not set.
     */
    public function testGetNonexistentConnection()
    {
        $config = $this->getConfigFilled();
        $config->getConnection('nonexistent_connection');
    }

    public function testGetConnection()
    {
        $config = $this->getConfigFilled();
        $this->addConfigMySQL($config);

        $connection = $config->getConnection($this->connectionName);
        $this->assertEquals('pgsql:host=localhost;dbname=postgres;port=5432;', $connection['connectionString']);
        $this->assertEquals($this->driver, $connection['driver']);
        $this->assertEquals($this->username, $connection['username']);
        $this->assertEquals($this->password, $connection['password']);
        $this->assertEquals($this->charset, $connection['charset']);
        $this->assertEquals($this->defaultSchema, $connection['schema']);

        $connection = $config->getConnection('mysql');
        $this->assertEquals('mysql:host=localhost;dbname=database;port=5432;', $connection['connectionString']);
    }

    private function addConfigMySQL(&$config)
    {
        $config->addConfig(
            'mysql',
            'luke',
            'obiwan',
            'localhost',
            'database',
            'mysql',
            $this->port);
    }

    private function getConfigFilled()
    {
        return (new Config)->addConfig(
            $this->driver,
            $this->username,
            $this->password,
            $this->host,
            $this->database,
            $this->connectionName,
            $this->port,
            'utf8',
            $this->defaultSchema
        );
    }
}
