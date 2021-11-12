#!/usr/bin/php
<?php

namespace Assegai;

require_once 'bootstrap.php';

use Assegai\LIB\DBFactory;
use Assegai\LIB\Logging\Logger;
use Assegai\LIB\Menus\Menu;
use Assegai\LIB\Menus\MenuItem;

$dbType = 'mysql';
$dbName = null;

function nextLine(string $message = '$'): string
{
  echo "$message: \e[1;34m";
  $line = trim(fgets(STDIN));
  echo "\e[0m";
  return $line;
}

function isExitRequest(string $input): bool
{
  return in_array(strtolower($input), ['x', 'quit', 'exit', 'kill']);
}

$configFilePath = 'app/config/default.php';
$localConfigFilePath = 'app/config/local.php';
$prodConfigFilePath = 'app/config/production.php';
$message = '';
$config = [];
$availableDatabases = [];
$selectedDatabase = null;

if (!file_exists($configFilePath))
{
  exit("\e[1;31mMissing file: $configFilePath\e[0m\n");
}

$config = require($configFilePath);

if (file_exists("$workingDirectory/$localConfigFilePath"))
{
  $localConfig = require("$workingDirectory/$localConfigFilePath");
  if (is_array($localConfig))
  {
    $config = array_merge($config, $localConfig);
  }
}

if (!isset($config['databases']))
{
  exit("\e[1;31mInvalid config: 'databases' is not defined.\e[0m\n");
}

$menu = new Menu(title: 'Available Databases Types');
$availableDatabases = array_keys($config['databases']);

foreach ($availableDatabases as $index => $db)
{
  $menu->add(new MenuItem(value: $db));
}
$menu->add(new MenuItem(value: 'Quit', index: 'x'));
$choice = $menu->prompt();

if (is_null($choice))
{
  exit("\e[1;31mInvalid choice\e[0m\n");
}

if (isExitRequest(input: $choice->value()))
{
  exit(-1);
}

if(!isset($config['databases'][$choice->value()]))
{
  exit("\e[1;31mInvalid choice\e[0m\n");
}

$selectedDatabaseType = $choice->value();

$menu = new Menu(title: "Available Databases ($selectedDatabaseType)");
$availableDatabases = array_keys($config['databases'][$selectedDatabaseType]);

foreach ($availableDatabases as $index => $db)
{
  $menu->add(new MenuItem(value: $db));
}

$choice = $menu->prompt();

if(!isset($config['databases'][$selectedDatabaseType][$choice->value()]))
{
  exit("\e[1;31mInvalid choice\e[0m\n");
}

$config = $config['databases'][$selectedDatabaseType][$choice->value()];
$connection = DBFactory::getSQLConnection($config, dialect: $selectedDatabaseType);

# Create database if it doesn't exist

# Create an empty migrations directory that we can use to manage our schema
$migrationsDir = "$workingDirectory/migrations/$selectedDatabaseType/". $choice->value();
$initialMigrationDir = "$migrationsDir/00000000000000_initial_directory";

echo "\n";

if (!file_exists($initialMigrationDir))
{
  if (mkdir(directory: $initialMigrationDir, recursive: true))
  {
    Logger::logCreate(filename: str_replace("$workingDirectory/", '', $initialMigrationDir));
  }
  else
  {
    Logger::error(message: "Failed to create $initialMigrationDir");
  }
}

if (!file_exists($initialMigrationDir . '/up.sql'))
{
  if (fopen(filename: $initialMigrationDir . '/up.sql', mode: 'w'))
  {
    Logger::logCreate(filename: str_replace("$workingDirectory/", '', $initialMigrationDir . '/up.sql'));
  }
  else
  {
    Logger::error(message: "Failed to create $initialMigrationDir/up.sql");
  }
}

if (!file_exists($initialMigrationDir . '/down.sql'))
{
  if (fopen(filename: $initialMigrationDir . '/down.sql', mode: 'w'))
  {
    Logger::logCreate(filename: str_replace("$workingDirectory/", '', $initialMigrationDir . '/down.sql'));
  }
  else
  {
    Logger::error(message: "Failed to create $initialMigrationDir/down.sql");
  }
}

$dbName = $config['name'];
$migrationsTableSchema = "CREATE TABLE IF NOT EXISTS `$dbName`.`__migrations` (
  `migration` BIGINT(14) NOT NULL ,
  `ran_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`migration`)) ENGINE = InnoDB";

$statement = $connection->query($migrationsTableSchema);

if (is_bool($statement))
{
  exit($connection->errorInfo());
}

if (!$statement->execute())
{
  exit($connection->errorInfo());
}

Logger::logCreate('__migrations table');
Logger::log(message: "\nAll done!");

?>