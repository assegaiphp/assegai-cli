#!/usr/bin/php
<?php

namespace Assegai;

require_once 'bootstrap.php';

require_once 'database-selection.php';

use Assegai\LIB\Logging\Logger;

# Create database if it doesn't exist

# Create an empty migrations directory that we can use to manage our schema
$migrationsDir = "$workingDirectory/migrations/$selectedDatabaseType/". $choice->value();
$initialMigrationDir = "$migrationsDir/00000000000000_initial";

echo "\n";

if (!file_exists($initialMigrationDir))
{
  if (mkdir(directory: $initialMigrationDir, recursive: true))
  {
    Logger::logCreate(path: str_replace("$workingDirectory/", '', $initialMigrationDir));
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
    Logger::logCreate(path: str_replace("$workingDirectory/", '', $initialMigrationDir . '/up.sql'));
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
    Logger::logCreate(path: str_replace("$workingDirectory/", '', $initialMigrationDir . '/down.sql'));
  }
  else
  {
    Logger::error(message: "Failed to create $initialMigrationDir/down.sql");
  }
}

$dbName = $config['name'];
$migrationsTableSchema = "CREATE TABLE IF NOT EXISTS `$dbName`.`__assegai_schema_migrations` (
  `migration` VARCHAR(14) NOT NULL ,
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

Logger::logCreate('__assegai_schema_migrations table');
Logger::log(message: "\nAll done!");

?>