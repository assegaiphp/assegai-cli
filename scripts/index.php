#!/usr/bin/php
<?php

use Assegai\LIB\Menus\Menu;
use Assegai\LIB\Menus\MenuItem;

require_once 'bootstrap.php';

# List options
$mainMenu = new Menu('Available Commands');
$mainMenu->addRange([
  new MenuItem(value: 'config', description: 'Retrieves or sets Assegai configuration values in the assegai.json file for the workspace.'),
  new MenuItem(value: 'database', alias: 'd', description: 'Manage configured database schemas.'),
  new MenuItem(value: 'generate', alias: 'g', description: 'Generates and/or modifies files based on a schematic.'),
  new MenuItem(value: 'init', description: 'Create an empty Assegai workspace or reinitialize an existing one.'),
  new MenuItem(value: 'migration', description: 'Manage database migrations.'),
  new MenuItem(value: 'new', description: 'Generate Assegai application.'),
  new MenuItem(value: 'version', alias: 'v', description: 'Outputs Assegai CLI version.'),
]);