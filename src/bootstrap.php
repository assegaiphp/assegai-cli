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
  $filename = str_replace('Assegai', 'src', $filename);

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

function prompt(string $message = 'Enter choice', ?string $defaultValue = null, ?int $attempts = null): string
{
  $defaultHint = '';
  if (!empty($defaultValue))
  {
    $defaultHint = "($defaultValue) ";
  }

  $isValid = false;
  $attemptsLeft = $attempts;

  do
  {
    printf("%s?%s %s: %s%s", Color::GREEN, Color::RESET, $message, $defaultHint, Color::BLUE);
    $line = trim(fgets(STDIN));
    echo Color::RESET;

    if (is_null($attemptsLeft))
    {
      $isValid = true;
    }
    else
    {
      --$attemptsLeft;
      if (!empty($line))
      {
        $isValid = true;
      }
      else if ($attemptsLeft === 0)
      {
        exit(1);
      }
      else
      {
        printf("%sInvalid input: %d attempts left%s\n", Color::MAGENTA, $attemptsLeft, Color::RESET);
      }
    }
  }
  while(!$isValid);

  if (empty($line) && !is_null($defaultValue))
  {
    $line = $defaultValue;
  }

  return $line;
}

function bytes_format(?int $bytes): string
{
  if (is_null($bytes))
  {
    $bytes = 0;
  }
  return match (true) {
    $bytes < 1024 => "$bytes bytes",
    $bytes < 1048576 => number_format($bytes / 1024, 2) . " MB",
    $bytes < 1073741824 => number_format($bytes / 1048576, 2) . " GB",
    default => "$bytes bytes"
  };
}

function getHeader(): string
{
  global $assegaiPath;
  $content = file_get_contents(sprintf("%s/src/header.txt", $assegaiPath));
  $output = Color::RED;
  $output .= $content;
  $output .= Color::RESET;
  return $output;
}

function printHeader(): void
{
  echo getHeader() . "\n";
}

function getVersion(): string
{
  return '1.0.0';
}

function printVersion(): void
{
  echo getVersion();
}