#!/usr/bin/php
<?php

namespace Assegai\CLI\Commands\Migration;

use Assegai\CLI\LIB\DatabaseSelector;
use Assegai\CLI\LIB\Migration\Migrator;

list($type, $name) = match (count($args)) {
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

$migrator->revert();