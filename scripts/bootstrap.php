#!/usr/bin/php
<?php

use Assegai\LIB\Color;
use Assegai\LIB\Logging\Logger;
use Assegai\LIB\WorkspaceManager;

$args = array_slice($argv, 1);
$workingDirectory = exec(command: 'pwd');
$assegaiPath = exec(command: "which assegai");

if (!empty($assegaiPath))
{
  $assegaiPath = explode('/', substr($assegaiPath, 1));
  $assegaiPath = '/' . implode('/', array_slice($assegaiPath, 0, -1));
}

spl_autoload_register(function ($class) {
  global $assegaiPath;
  $filename = str_replace('\\', DIRECTORY_SEPARATOR, $assegaiPath . "\\$class") . '.php';
  $filename = str_replace('Assegai', 'scripts', $filename);

  if (file_exists($filename))
  {
    require_once  $filename;
  }
});

/**
 * Checks the `workding directory` for an `assegai.json` file. If no 
 * project file is found it will log an error.
 * 
 * @param null|string $commandName The name of the command that requires 
 * an assegai workspace to run.
 */
function checkWorkspace(?string $commandName = null): void
{
  if (is_null($commandName))
  {
    $commandName = substr(basename(__FILE__), 0, -4);
  }

  if (!WorkspaceManager::isAssegaiWorkspace())
  {
    Logger::error("The $commandName command requires to be run in an Assegai workspace, but a project definition could not be found.", terminateAfterLog: true);
  }
}

function prompt(string $message = 'Enter choice', ?string $defaultValue = null): string
{
  $defaultHint = '';
  if (!empty($defaultValue))
  {
    $defaultHint = "($defaultValue)";
  }

  printf("%s?%s %s: %s%s", Color::GREEN, Color::RESET, $message, $defaultHint, Color::BLUE);
  $line = trim(fgets(STDIN));
  echo Color::RESET;

  return $line;
}