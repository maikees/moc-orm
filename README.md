# MOC ORM

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/maikees/moc-orm/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/maikees/moc-orm/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/maikees/moc-orm/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/maikees/moc-orm/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/maikees/moc-orm/badges/build.png?b=master)](https://scrutinizer-ci.com/g/maikees/moc-orm/build-status/master)

MOC ORM is a fast object-relational mapper (ORM) for PHP.

## Install

`composer require moc-solucoes/moc-orm`

## Connection

### Usage connection

1. Extends your model to class Model, using namespace `MocOrm\Model\Model`;
2. Set the namespace to connection
3. Initialize the connection using static method `Connection::initialize()`

 ```
 @return Connection
 ```

4. Add the configurations using the method addConfig, accepts various configurations
 ```
 Arguments:
 $connection->addConfig('driver', 'user', 'password', 'host', 'database', connectionName', 'port');
 Driver options ['mysql', 'pgsql'] -- Mysql, postgres
 @return Connection
 ```
5. Set connection for active using the method `setConnection()`
 ```
 $connection->setConnection('connectionName');
 @return Connection
 ```
6. if needed change the connection using the method `changeConnection()`
 ```
 $connection->setConnection('connectionName');
 @return Connection
 ```
7. Get current connection using the method `getCurrentConnectionName()`
 ```
 $connection->getCurrentConnectionName();
 @return String Connection name
 ```
8. Get all previous settings, using method `getConfig()`
 ```
 $connection->getConfig();
 @return array on connection string
 ```
9. Get last performed query using method `getLastPerformedQuery()`
 ```
 @return array
 ```
10. Get all performed query using method `getPerformedQuery()`
 ```
 @return array
 ```

### Examples

You can find practical examples usage in `examples` path.

## License

This library is released under the [MIT license](https://github.com/maikees/moc-orm/blob/master/LICENSE).
