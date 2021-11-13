#!/usr/bin/php
<?php
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