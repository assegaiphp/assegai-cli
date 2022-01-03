#!/usr/bin/php
<?php

namespace Assegai\CLI\Commands\Migration;

use Assegai\CLI\LIB\Color;
use Assegai\CLI\LIB\DatabaseSelector;
use Assegai\CLI\LIB\Migration\Migrator;

function readLine(string $message = '$'): string
{
  printf("%s: %s", $message, Color::BLUE);
  $line = trim(fgets(STDIN));
  echo Color::RESET;
  return $line;
}

function promptForMigrationName(): string
{
  $name = readLine(message: sprintf("%s? %sMigration name", Color::GREEN, Color::RESET));
  return $name;
}

list($databaseType, $databaseName, $migrationName) = match (count($args)) {
  1       => [null, null, null],
  2       => [$args[1], null, null],
  default => array_slice($args, 1)
};

$databaseSelector = new DatabaseSelector(
  databaseType: $databaseType,
  databaseName: $databaseName
);
$databaseSelector->run();

$selectedDatabaseType = $databaseSelector->databaseType();
$selectedDatabaseName = $databaseSelector->databaseName();

$migrationsDir = "$workingDirectory/migrations/$selectedDatabaseType/$selectedDatabaseName";

$migrator = new Migrator(connection: $databaseSelector->connection(), migrationDirectory: $migrationsDir);

if (empty($migrationName))
{
  $migrationName = promptForMigrationName();
}

$migrator->generate(name: $migrationName);