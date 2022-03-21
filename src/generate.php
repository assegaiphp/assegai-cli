#!/usr/bin/php
<?php

use Assegai\CLI\LIB\Logging\Logger;
use Assegai\CLI\LIB\Menus\Menu;
use Assegai\CLI\LIB\Menus\MenuItem;
use Assegai\CLI\LIB\Menus\MenuOptions;

checkWorkspace(commandName: 'generate');

$commandsMenu = new Menu(
  title: 'Available schematics:',
  items: [
    new MenuItem(value: 'application', description: 'Generate a new application workspace.'),
    new MenuItem(value: 'class', alias: 'cl', description: 'Generate a new class.'),
    new MenuItem(value: 'controller', alias: 'co', description: 'Generate a controller declaration.'),
    new MenuItem(value: 'entity', description: 'Generate an entity declaration.'),
    new MenuItem(value: 'enum', description: 'Generate an enumeration declaration.'),
    new MenuItem(value: 'feature', alias: 'f', description: 'Generate a new CRUD resource.'),
    new MenuItem(value: 'guard', alias: 'gu', description: 'Generate a guard declaration.'),
    new MenuItem(value: 'module', alias: 'mo', description: 'Generate a module declaration.'),
    new MenuItem(value: 'repository', alias: 'r', description: 'Generate a repository declaration.'),
    new MenuItem(value: 'service', alias: 's', description: 'Generate a service declaration.'),
  ],
  description: 'Usage: assegai generate <schematic> [options]',
  options: new MenuOptions(showDescriptions: true, showIndexes: false)
);
$optionsMenu = new Menu(
  title: 'Available Options:',
  items: [
    new MenuItem(value: '--help', description: 'Output usage information.')
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
  array_shift($args);
  list($command) = $args;

  $command = match ($command) {
    'co'  => 'controller',
    'cl'  => 'class',
    'f'   => 'feature',
    'gu'  => 'guard',
    'mo'  => 'module',
    'r'   => 'repository',
    's'   => 'service',
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
      $filename = "$assegaiPath/src/Commands/Generate/$filename";
      
      if (!file_exists($filename))
      {
        $filename = str_replace("$assegaiPath/src/", '', $filename);
        Logger::error("Missing file definition: $filename");
      }

      require_once $filename;
    }
  }
  else
  {
    help();
  }
}