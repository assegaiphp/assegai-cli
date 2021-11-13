#!/usr/bin/php
<?php

namespace Assegai;

use Assegai\LIB\DBFactory;
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

function isQuitRequest(string $input): bool
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

$menu = new Menu(title: 'Database Types');
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

if (isQuitRequest(input: $choice->value()))
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
$menu->add(new MenuItem(value: 'Quit', index: 'x'));
$choice = $menu->prompt();

if (is_null($choice))
{
  exit("\e[1;31mInvalid choice\e[0m\n");
}

if (isQuitRequest(input: $choice->value()))
{
  exit(-1);
}

if(!isset($config['databases'][$selectedDatabaseType][$choice->value()]))
{
  exit("\e[1;31mInvalid choice\e[0m\n");
}

$config = $config['databases'][$selectedDatabaseType][$choice->value()];
$connection = DBFactory::getSQLConnection($config, dialect: $selectedDatabaseType);
