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

  public function buildApplication(?string $name): void
  {
  }

  public function buildClass(?string $name): void
  {
  }
  
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
        Logger::error("Could not create modules directory.", terminateAfterLog: true);
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
      WorkspaceManager::updateModule(moduleName: $name, targetArray: 'controllers', newEntry: "${name}Controller");
    }
  }

  public function buildEntity(?string $path): void
  {
  }

  public function buildFeature(?string $name): void
  {
    $this->buildModule(name: $name);
    
    usleep(300000);
    $this->buildController(name: $name);
    
    usleep(300000);
    $this->buildService(name: $name);
    
    $choice = prompt(message: 'Would you like to generate a repository', defaultValue: 'Y');

    if (in_array(strtolower($choice), ['y', 'yes', 'yeah']))
    {
      $this->buildRepository(name: $name);
    }
    
    $choice = prompt(message: 'Would you like to generate an entity', defaultValue: 'Y');
    
    if (in_array(strtolower(trim($choice)), ['y', 'yes', 'yeah']))
    {
      $entityName = prompt(message: 'Entity name', attempts: 3);
      $this->buildEntity(path: $entityName);
    }
  }

  public function buildModule(?string $name): void
  {
    if (empty($name))
    {
      $name = prompt('Module name', attempts: 3);
    }

    $templatePath = $this->templateDirectory() . "/Module.Template.php";
    $modulesDirectory = $this->modulesDirectory();
    $featureDirectory = "$modulesDirectory/$name";
    $featureDirectoryRelative = $this->relativeWorkingDirectoryPath(path: $featureDirectory);
    $targetFile = "$featureDirectory/${name}Module.php";
    $targetFileRelative = $this->relativeWorkingDirectoryPath(path: $targetFile);

    if (!file_exists($templatePath))
    {
      Logger::error("Missing schematic template.");
    }

    if (!file_exists($modulesDirectory))
    {
      if (!mkdir($modulesDirectory))
      {
        Logger::error("Could not create modules directory.", terminateAfterLog: true);
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

    $filesize = file_put_contents(filename: $targetFile, data: $content);

    if ($filesize === false)
    {
      Logger::error("Could not create $targetFileRelative", terminateAfterLog: true);
    }

    Logger::logCreate(path: $targetFileRelative, filesize: $filesize);

    $modulePath = $featureDirectory . "/${name}Module.php";

    if (file_exists($modulePath))
    {
      WorkspaceManager::updateRoutes(moduleName: $name);
    }
  }

  public function buildRepository(?string $name): void
  {
    if (empty($name))
    {
      $name = prompt('Repository name', attempts: 3);
    }

    $templatePath = $this->templateDirectory() . "/Repository.Template.php";
    $modulesDirectory = $this->modulesDirectory();
    $featureDirectory = "$modulesDirectory/$name";
    $featureDirectoryRelative = $this->relativeWorkingDirectoryPath(path: $featureDirectory);
    $targetFile = "$featureDirectory/${name}Repository.php";
    $targetFileRelative = $this->relativeWorkingDirectoryPath(path: $targetFile);

    if (!file_exists($templatePath))
    {
      Logger::error("Missing schematic template.");
    }

    if (!file_exists($modulesDirectory))
    {
      if (!mkdir($modulesDirectory))
      {
        Logger::error("Could not create modules directory.", terminateAfterLog: true);
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
    $content = str_replace('TableName', strtolower($name), $content);

    $filesize = file_put_contents(filename: $targetFile, data: $content);

    if ($filesize === false)
    {
      Logger::error("Could not create $targetFileRelative", terminateAfterLog: true);
    }

    Logger::logCreate(path: $targetFileRelative, filesize: $filesize);

    $modulePath = $featureDirectory . "/${name}Module.php";

    if (file_exists($modulePath))
    {
      WorkspaceManager::updateModule(moduleName: $name, targetArray: 'controllers', newEntry: "${name}Controller");
    }
  }

  public function buildService(?string $name): void
  {
    if (empty($name))
    {
      $name = prompt('Service name', attempts: 3);
    }

    $templatePath = $this->templateDirectory() . "/Service.Template.php";
    $modulesDirectory = $this->modulesDirectory();
    $featureDirectory = "$modulesDirectory/$name";
    $featureDirectoryRelative = $this->relativeWorkingDirectoryPath(path: $featureDirectory);
    $targetFile = "$featureDirectory/${name}Service.php";
    $targetFileRelative = $this->relativeWorkingDirectoryPath(path: $targetFile);

    if (!file_exists($templatePath))
    {
      Logger::error("Missing schematic template.");
    }

    if (!file_exists($modulesDirectory))
    {
      if (!mkdir($modulesDirectory))
      {
        Logger::error("Could not create modules directory.", terminateAfterLog: true);
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

    $filesize = file_put_contents(filename: $targetFile, data: $content);

    if ($filesize === false)
    {
      Logger::error("Could not create $targetFileRelative", terminateAfterLog: true);
    }

    Logger::logCreate(path: $targetFileRelative, filesize: $filesize);

    $modulePath = $featureDirectory . "/${name}Module.php";

    if (file_exists($modulePath))
    {
      WorkspaceManager::updateModule(moduleName: $name, targetArray: 'providers', newEntry: "${name}Service");
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