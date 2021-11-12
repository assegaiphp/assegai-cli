<?php

namespace Assegai\LIB;

use Exception;
use PDO;

/**
 * The `DBFactory` class houses static methods for creating **Database 
 * connection objects**.
 */
final class DBFactory
{
  private static array $connections = [
    'mysql'   => [],
    'mariadb' => [],
    'pgsql'   => [],
    'sqlite'  => [],
    'mongodb' => [],
  ];

  public static function getSQLConnection(array $config, ?string $dialect = 'mysql'): PDO {
    return match ($dialect) {
      'mysql'       => DBFactory::getMySQLConnection(config: $config),
      'mariadb'     => DBFactory::getMariaDBConnection(config: $config),
      'pgsql'       => DBFactory::getPostgreSQLConnection(config: $config),
      'postgresql'  => DBFactory::getPostgreSQLConnection(config: $config),
      'sqlite'      => DBFactory::getSQLiteConnection(config: $config),
      default       => DBFactory::getMySQLConnection(config: $config)
    };
  }

  public static function getMySQLConnection(array $config): PDO
  {
    $type = 'mysql';
    try
    {
      extract($config);
      DBFactory::$connections[$type][$name] = new PDO(
        dsn: "mysql:host=$host;port=$port;dbname=$name",
        username: $user,
        password: $password
      );
    }
    catch (Exception $e)
    {
      exit($e->getMessage());
    }

    return DBFactory::$connections[$type][$name];
  }

  public static function getMariaDBConnection(array $config): PDO
  {
    $type = 'mariadb';

    try
    {
      extract($config);
      DBFactory::$connections[$type][$name] = new PDO(
        dsn: "mysql:host=$host;port=$port;dbname=$name",
        username: $user,
        password: $password
      );
    }
    catch (Exception $e)
    {
      exit($e->getMessage());
    }

    return DBFactory::$connections[$type][$name];
  }

  public static function getPostgreSQLConnection(array $config): PDO
  {
    $type = 'pgsql';

    try
    {
      extract($config);
      DBFactory::$connections[$type][$name] = new PDO(
        dsn: "pgsql:host=$host;port=$port;dbname=$name",
        username: $user,
        password: $password
      );
    }
    catch (Exception $e)
    {
      exit($e->getMessage());
    }

    return DBFactory::$connections[$type][$name];
  }

  public static function getSQLiteConnection(array $config): PDO
  {
    $type = 'sqlite';

    try
    {
      extract($config);
      DBFactory::$connections[$type][$name] = new PDO( dsn: "sqlite:$path" );
    }
    catch (Exception $e)
    {
      exit($e->getMessage());
    }

    return DBFactory::$connections[$type][$name];
  }

  public static function getMongoDbConnection(array $config): PDO
  {
    $type = 'mongodb';
    extract($config);

    if (!isset($name))
    {
      exit("\e[0;31mMissing name\e[0m");
    }

    try
    {

    }
    catch (Exception $e)
    {
      exit($e->getMessage());
    }

    return DBFactory::$connections[$type][$name];
  }
}
