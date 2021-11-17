#!/usr/bin/env php
<?php

namespace Assegai\LIB\Generation;

use Assegai\LIB\Logging\Logger;
use Assegai\LIB\WorkspaceManager;

final class SchematicBuilder
{
  public function __construct(
    private bool $verbose = false
  ) { }

  public function buildController(?string $name): void
  {
    if (empty($name))
    {
      $name = prompt('Controller name', attempts: 3);
    }

    $templatePath = $this->templateDirectory() . "/Controller.Template.php";
    $modulesDirectory = $this->modulesDirectory();
    $featureDirectory = "$modulesDirectory/$name";
    $featureDirectoryRelative = $this->relativeWorkingDirectoryPath(path: $featureDirectory);
    $targetFile = "$featureDirectory/${name}Controller.php";
    $targetFileRelative = $this->relativeWorkingDirectoryPath(path: $targetFile);

    if (!file_exists($templatePath))
    {
      Logger::error("Missing schematic template.");
    }

    if (!file_exists($modulesDirectory))
    {
      if (!mkdir($modulesDirectory))
      {
        Logger::error("Could not created modules directory.", terminateAfterLog: true);
      }

      if ($this->verbose)
      {
        Logger::logCreate('app/src/Modules');
      }
    }

    if (!file_exists($featureDirectory))
    {
      if (!mkdir($featureDirectory))
      {
        Logger::error("Could not create " . $featureDirectoryRelative, terminateAfterLog: true);
      }

      if ($this->verbose)
      {
        Logger::logCreate($featureDirectoryRelative);
      }
    }

    if (file_exists($targetFile))
    {
      Logger::error("$targetFileRelative already exists!", terminateAfterLog: true);
    }
    $content = file_get_contents($templatePath);
    $content = str_replace('ModuleName', $name, $content);
    $content = str_replace('PathName', strtolower($name), $content);

    $filesize = file_put_contents(filename: $targetFile, data: $content);

    if ($filesize === false)
    {
      Logger::error("Could not create $targetFileRelative", terminateAfterLog: true);
    }

    Logger::logCreate(path: $targetFileRelative, filesize: $filesize);

    $modulePath = $featureDirectory . "/${name}Module.php";

    if (file_exists($modulePath))
    {
      WorkspaceManager::updateModule(moduleName: $name, targetArray: 'controllers', newEntry: $name);
    }
  }

  public function templateDirectory(): string
  {
    global $assegaiPath;
    return sprintf("%s/templates", $assegaiPath);
  }

  public function modulesDirectory(): string
  {
    global $workingDirectory;
    return sprintf("%s/app/src/Modules", $workingDirectory);
  }

  public function relativeWorkingDirectoryPath(string $path): string
  {
    global $workingDirectory;
    return str_replace($workingDirectory, '', $path);
  }
}