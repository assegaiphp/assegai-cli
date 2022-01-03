#!/usr/bin/env php
<?php

namespace Assegai\CLI\Commands\Database;

use Assegai\CLI\LIB\DatabaseSelector;
use Assegai\CLI\LIB\Util\Config;

list($type, $name) = match (count($args)) {
  1       => [null, null],
  2       => [$args[1], null],
  default => array_slice($args, 1),
};

$databaseSelector = new DatabaseSelector(
  databaseType: $type,
  databaseName: $name
);
$databaseSelector->run();

$selectedDatabaseType = $databaseSelector->databaseType();

$CONFIG_PATH = 'app/config/';
$configFilename = $CONFIG_PATH . 'default.php';
$localConfigFilename = $CONFIG_PATH . 'local.php';
$localConfig = [];

$config = require($configFilename);

if (file_exists($localConfigFilename))
{
  $localConfig = require($localConfigFilename);
}

$config = array_merge($config, $localConfig);
$dbList = array_keys($config['databases'][$selectedDatabaseType]);

exit(implode(' ', $dbList) . "\n");