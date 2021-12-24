#!/usr/bin/php
<?php

namespace Assegai\CLI\Commands\Database;

use Assegai\CLI\LIB\Color;
use Assegai\CLI\LIB\Logging\Logger;

$sourcePath         = sprintf("%s/app/src", $workingDirectory);
$modulesDir         = sprintf("%s/Modules", $sourcePath);

function readLine(string $message = ''): string
{
  printf("%s: %s", $message, Color::BLUE);
  $line = trim(fgets(STDIN));
  echo Color::RESET;
  return $line; 
}

spl_autoload_register(function ($class) {
  global $sourcePath;
  $targetPath = str_replace('/src', '', $sourcePath);
  $filename   = str_replace('\\', DIRECTORY_SEPARATOR, $targetPath . "\\$class") . '.php';
  $filename   = str_replace('Assegai', 'src', $filename);

  if (file_exists($filename))
  {
    require_once $filename;
  }
});

if (!file_exists($modulesDir))
{
  Logger::error(message: 'Modules directory not found.', exit: true);
}

$moduleNames = scandir(directory: $modulesDir);

if (count($moduleNames) < 3)
{
  Logger::log('Nothing to do', exit: true);
}

$moduleNames = array_slice($moduleNames, 2);

use Assegai\Database\Schema;
use Assegai\Database\SchemaOptions;

foreach($moduleNames as $moduleName)
{
  $featureDir = sprintf("%s/%s", $modulesDir, $moduleName);

  $featureFiles = array_slice(scandir($featureDir), 2);

  foreach ($featureFiles as $filename)
  {
    if (str_ends_with($filename, 'Entity.php'))
    {
      $entityName = "Assegai\\Modules\\$moduleName\\" . substr($filename, 0, -4);
      $entity = new $entityName;
      $dbName = $entity->database();

      if (is_null($dbName))
      {
        $dbName = readLine(message: sprintf("%s? %s%s", Color::GREEN, Color::RESET, 'Database name'));
      }
      
      if (Schema::create(entityClass: $entityName, options: new SchemaOptions(dbName: $dbName)))
      {
        Logger::logCreate(path: $entityName);
      }
      else
      {
        Logger::error("Failed to sync $entityName");
      }
      
      // $schema = new Assegai\Database\Schema;
      // echo file_get_contents(sprintf("%s/%s", $featureDir, $filename)) . "\n\n";
    }
  }
}
