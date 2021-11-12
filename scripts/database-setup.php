#!/usr/bin/php
<?php

$dbType = 'mysql';
$dbName = null;

function nextLine(string $message = '$'): string
{
  echo "$message: \e[1;34m";
  $line = trim(fgets(STDIN));
  echo "\e[0m";
  return $line;
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

if (file_exists($localConfigFilePath))
{
  $localConfig = require($localConfigFilePath);
  if (is_array($localConfig))
  {
    $config = array_merge($config, );
  }
}

if (!isset($config['databases']))
{
  exit("\e[1;31mInvalid config: 'databases' is not defined.\e[0m\n");
}

echo "\e[1;33mAvailable Databases Types:\e[0m\n\n";
$availableDatabases = array_keys($config['databases']);

foreach ($availableDatabases as $index => $db)
{
  echo "\e[1;34m$index) \e[0m$db\n";
}

$choice = nextLine(message: "\nEnter choice (e.g 0)");

if (is_numeric($choice))
{
  if (!key_exists($choice, $availableDatabases))
  {
    exit("\e[1;31mInvalid choice\e[0m\n");
  }
  
  $choice = $availableDatabases[$choice];
}

if(!isset($config['databases'][$choice]))
{
  exit("\e[1;31mInvalid choice\e[0m\n");
}

$selectedDatabaseType = $config['databases'][$choice];

echo "\n\e[1;33mAvailable Databases ($choice):\e[0m\n\n";
$availableDatabases = array_keys($selectedDatabaseType);

foreach ($availableDatabases as $index => $db)
{
  echo "\e[1;34m$index) \e[0m$db\n";
}

$choice = nextLine(message: "\nEnter choice (e.g 0)");


// echo "\e[1;32mSelected Database:\e[0m\n\n" . var_export($selectedDatabase, true) . "\n";

?>