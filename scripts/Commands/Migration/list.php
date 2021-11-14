#!/usr/bin/php
<?php

namespace Assegai\Commands\Migration;

use Assegai\LIB\DatabaseSelector;
use Assegai\LIB\Logging\Logger;
use Assegai\LIB\Migration\Migrator;

list($type, $name) = match(count($args)) {
  1       => [null, null],
  2       => [$args[1], null],
  default => array_slice($args, 1)
};

$databaseSelector = new DatabaseSelector(
  databaseType: $type,
  databaseName: $name
);
$databaseSelector->run();

$selectedDatabaseType = $databaseSelector->databaseType();
$selectedDatabaseName = $databaseSelector->databaseName();

$migrationsDir = "$workingDirectory/migrations/$selectedDatabaseType/$selectedDatabaseName";

$migrator = new Migrator(connection: $databaseSelector->connection(), migrationDirectory: $migrationsDir);

$migrations = $migrator->listMigrations();

if ($migrations === false)
{
  Logger::error(message: "Nothing to do\n", terminateAfterLog: true);
}

foreach ($migrations as $migration)
{
  printf("  %s\n", $migration);
}

echo "\n";