#!/usr/bin/php
<?php

namespace Assegai\CLI\Commands\Database;

use Assegai\CLI\LIB\DatabaseSelector;
use Assegai\CLI\LIB\Logging\Logger;

list($type, $name) = match (count($args)) {
  1       => [null, null],
  2       => [$args[1], null],
  default => array_slice($args, 1),
};

$databaseSelector = new DatabaseSelector(
  databaseType: $type,
  databaseName: $name,
  promptToCreate: true # If database doesn't exist, create it
);
$databaseSelector->run();

$selectedDatabaseType = $databaseSelector->databaseType();
$selectedDatabaseName = $databaseSelector->databaseName();

$connection           = $databaseSelector->connection();

$migrationsDir = "$workingDirectory/migrations/$selectedDatabaseType/$selectedDatabaseName";
$initialMigrationDir = "$migrationsDir/00000000000000_initial";

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

# 1. Create database if it doesn't exist

# 2. Create migrations table
$migrationsTableSchema = "CREATE TABLE IF NOT EXISTS `$selectedDatabaseName`.`__assegai_schema_migrations` (
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