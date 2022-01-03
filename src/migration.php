#!/usr/bin/php
<?php

namespace Assegai\CLI;

use Assegai\CLI\LIB\Menus\Menu;
use Assegai\CLI\LIB\Menus\MenuItem;
use Assegai\CLI\LIB\Menus\MenuOptions;

require_once 'bootstrap.php';

$commandsMenu = new Menu(
  title: 'Migration Commands:',
  items: [
    new MenuItem(
      value: 'generate',
      alias: 'g',
      description: 'Creates a new migration.',
      fullDescription: 'Creates two empty files in the required structure, up.sql and down.sql. Migrations provide a means for evolving the database schema over time. Each migration can be applied (up.sql) or reverted (down.sql). Applying and immediately reverting a migration should leave your database schema unchanged.'
    ),
    new MenuItem(value: 'list', description: 'Displays all migrations for a given database.'),
    new MenuItem(value: 'redo', description: 'Runs down.sql and then up.sql for the most recent migration.'),
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

function help(?string $command = null)
{
  global $commandsMenu, $optionsMenu;
  if (is_null($command))
  {
    printf("%s\n%s\n", $commandsMenu, $optionsMenu);
  }
  else
  {
    $commandsMenu->describeItem(itemValueOrIndex: $command);
  }
}

array_shift($args);

if (empty($args))
{
  help();
}
else
{
  list($command) = $args;

  $command = match($command) {
    'g' => 'generate',
    default => $command
  };
  
  if ($commandsMenu->hasItemWithValue(valueOrAlias: strtolower($command)))
  {
    list($lastArg) = array_slice($args, -1);
    if ($lastArg === '--help')
    {
      $commandsMenu->describeItem(itemValueOrIndex: $command);
    }
    else
    {
      $filename = strtolower($command) . '.php';
      require_once "Commands/Migration/$filename";
    }
  }
  else
  {
    help();
  }
}