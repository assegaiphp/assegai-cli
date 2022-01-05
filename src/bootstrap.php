#!/usr/bin/php
<?php
require_once 'functions.php';

$pid = getmypid();

if (!cli_set_process_title(title: 'assegai'))
{
  exit("Unable to set process title for PID $pid...\n");
}

use Assegai\CLI\LIB\Menus\Menu;
use Assegai\CLI\LIB\Menus\MenuItem;
use Assegai\CLI\LIB\Menus\MenuOptions;

$args = array_slice($argv, 1);
$workingDirectory = exec(command: 'pwd');
$assegaiPath = exec(command: "which assegai");
$assegaiConfig = "$workingDirectory/assegai.json";



if (!empty($assegaiPath))
{
  $assegaiPath = explode('/', substr($assegaiPath, 1));
  $assegaiPath = '/' . implode('/', array_slice($assegaiPath, 0, -1));
}

spl_autoload_register(function ($class) {
  global $assegaiPath;
  $filename = str_replace('\\', DIRECTORY_SEPARATOR, $assegaiPath . "\\$class") . '.php';
  $filename = str_replace('Assegai' . DIRECTORY_SEPARATOR . 'CLI', 'src', $filename);

  if (file_exists($filename))
  {
    require_once  $filename;
  }
});

# List options
$mainMenu = new Menu(
  title: 'Available Commands',
  options: new MenuOptions(
    showDescriptions: true,
    showIndexes: false
  )
);
$mainMenu->addRange([
  new MenuItem(value: 'config', description: 'Retrieves or sets Assegai configuration values in the assegai.json file for the workspace.'),
  new MenuItem(value: 'database', alias: 'd', description: 'Manages configured database schemas.'),
  new MenuItem(value: 'generate', alias: 'g', description: 'Generates and/or modifies files based on a schematic.'),
  new MenuItem(value: 'info', description: 'Displays Assegai project details.'),
  new MenuItem(value: 'init', description: 'Creates an empty Assegai workspace or reinitializes an existing one.'),
  new MenuItem(value: 'lint', description: 'Runs the code linter.'),
  new MenuItem(value: 'migration', description: 'Manages database migrations.'),
  new MenuItem(value: 'new', description: 'Generates a new Assegai application.'),
  new MenuItem(value: 'serve', description: 'Starts a local development server.'),
  new MenuItem(value: 'test', alias: 't', description: 'Runs unit tests in a project.'),
  new MenuItem(value: 'update', alias: 'u', description: 'Updates your application and its dependencies. See https://update.assegai.ml/'),
  new MenuItem(value: 'version', alias: 'v', description: 'Outputs Assegai CLI version.'),
]);
