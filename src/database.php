#!/usr/bin/php
<?php

use Assegai\CLI\LIB\Menus\Menu;
use Assegai\CLI\LIB\Menus\MenuItem;
use Assegai\CLI\LIB\Menus\MenuOptions;

require_once 'bootstrap.php';

$commandsMenu = new Menu(
  title: 'Database Commands:',
  items: [
    new MenuItem(value: 'reset', description: 'Drops the database specified in your config if it can, and then runs assegai database setup'),
    new MenuItem(value: 'setup', description: 'Creates and/or initializes a database.'),
    new MenuItem(value: 'sync', description: 'Sync the database with module entity schemas.'),
  ],
  description: 'Usage: assegai database [command] [options]',
  options: new MenuOptions(showDescriptions: true, showIndexes: false)
);
$optionsMenu = new Menu(
  title: 'Available Options:',
  items: [
    new MenuItem(value: '--help', description: 'Shows helpful information about a command.')
  ],
  options: new MenuOptions(showDescriptions: true, showIndexes: false)
);

function help()
{
  global $commandsMenu, $optionsMenu;
  printf("%s\n%s\n", $commandsMenu, $optionsMenu);
}

if (empty($args))
{
  help();
}
else
{
  list($command) = $args;

  if ($commandsMenu->hasItemWithValue(valueOrAlias: strtolower($command)))
  {
    $filename = strtolower($command) . '.php';
    require_once "Commands/Database/$filename";
  }
  else
  {
    help();
  }
}