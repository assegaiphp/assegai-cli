#!/usr/bin/php
<?php

use Assegai\LIB\Menus\Menu;
use Assegai\LIB\Menus\MenuItem;
use Assegai\LIB\Menus\MenuOptions;

require_once 'bootstrap.php';

$commandsMenu = new Menu(
  title: 'Generate Commands:',
  items: [
    new MenuItem(value: 'application', description: ''),
    new MenuItem(value: 'controller', description: ''),
    new MenuItem(value: 'class', description: ''),
    new MenuItem(value: 'service', description: ''),
    new MenuItem(value: 'entity', description: ''),
    new MenuItem(value: 'module', description: ''),
    new MenuItem(value: 'feature', description: ''),
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

  if ($commandsMenu->hasItemWithValue(value: strtolower($command)))
  {
    $filename = strtolower($command) . '.php';
    require_once "Commands/Generate/$filename";
  }
  else
  {
    help();
  }
}