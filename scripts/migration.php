#!/usr/bin/php
<?php

namespace Assegai;

use Assegai\LIB\Menus\Menu;
use Assegai\LIB\Menus\MenuItem;
use Assegai\LIB\Menus\MenuOptions;

require_once 'bootstrap.php';

$commandsMenu = new Menu(
  title: 'Migration Commands:',
  items: [
    new MenuItem(value: 'generate', description: 'Drops the database specified in your config if it can, and then runs assegai database setup'),
    new MenuItem(value: 'list', description: 'Displays all migrations for a given database.'),
    new MenuItem(value: 'redo', description: 'Runs the down.sql and then the up.sql for the most recent migration.'),
    new MenuItem(value: 'revert', description: 'Runs the down.sql for the most recent migration.'),
    new MenuItem(value: 'run', description: 'Runs all pending migrations, as determined by diesel\'s internal schema table.'),
  ],
  description: 'Usage: assegai migration [command] [options]',
  options: new MenuOptions(showDescriptions: true, showIndexes: false)
);
$optionsMenu = new Menu(
  title: 'Available Options:',
  items: [
    new MenuItem(value: '--help', description: 'Shows helpful information about a command.')
  ],
  options: new MenuOptions(
    showIndexes: false,
    showDescriptions: true
  )
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

  if ($commandsMenu->hasItemWithValue(value: strtolower($command)))
  {
    $filename = strtolower($command) . '.php';
    require_once "Commands/Migration/$filename";
  }
  else
  {
    help();
  }
}