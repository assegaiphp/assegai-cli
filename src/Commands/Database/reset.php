#!/usr/bin/php
<?php

namespace Assegai\CLI\Commands\Database;

use Assegai\CLI\LIB\DatabaseSelector;
use Assegai\CLI\LIB\Logging\Logger;
use PDOException;

list($type, $name) = match (count($args)) {
  1       => [null, null],
  2       => [$args[1], null],
  default => array_slice($args, 1),
};

$databaseSelector = new DatabaseSelector(
  databaseType: $type,
  databaseName: $name,
);
$databaseSelector->run();

$selectedDatabaseType = $databaseSelector->databaseType();
$selectedDatabaseName = $databaseSelector->databaseName();

$connection           = $databaseSelector->connection();

try
{
  $statement = $connection->query(sprintf("DROP DATABASE IF EXISTS `%s`", $selectedDatabaseName));

  if ($statement === false)
  {
    Logger::error(message: sprintf("Couldn't DROP database `%s`", $selectedDatabaseName), exit: true);
  }
  else
  {
    Logger::logDelete(sprintf("%s database", $selectedDatabaseName));
  }
}
catch(PDOException $e)
{
  Logger::error($e->getMessage(), exit: true);
}

$args = [null, $selectedDatabaseType, $selectedDatabaseName];

require_once 'setup.php';