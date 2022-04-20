#!/usr/bin/env php
<?php

namespace Assegai\CLI\LIB\Generation;

use Assegai\CLI\Exceptions\NotImplementedException;
use Assegai\CLI\LIB\Color;
use Assegai\CLI\LIB\Logging\Logger;
use Assegai\CLI\LIB\WorkspaceManager;

final class SchematicBuilder
{
  public function __construct(
    private bool $verbose = false
  ) { }

  /**
   * Build an application shell
   * 
   * @param null|string $name The name of the application.
   */
  public function buildApplication(?string $name): void
  {
    // TODO: #48 feat(lib): implement buildApplication()
    throw new NotImplementedException();
  }

  /**
   * Build a class declaration
   * 
   * @param null|string $path The path and filename of the the class.
   */
  public function buildClass(?string $path): void
  {
    // TODO: #49 feat(lib): implement buildClass()
    throw new NotImplementedException();
  }

  /**
   * Build a controller declaration
   * 
   * @param null|string $name The name of the controller.
   */
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
      if (!mkdir(directory: $modulesDirectory, recursive: true))
      {
        Logger::error("Could not create modules directory.", exit: true);
      }

      if ($this->verbose)
      {
        Logger::logCreate('app/src/Modules');
      }
    }

    if (!file_exists($featureDirectory))
    {
      if (!mkdir(directory: $featureDirectory, recursive: true))
      {
        Logger::error("Could not create " . $featureDirectoryRelative, exit: true);
      }

      if ($this->verbose)
      {
        Logger::logCreate($featureDirectoryRelative);
      }
    }

    if (file_exists($targetFile))
    {
      Logger::error("$targetFileRelative already exists!", exit: true);
    }
    $content = file_get_contents($templatePath);
    $content = str_replace('ModuleName', $name, $content);
    $content = str_replace('PathName', strtolower($name), $content);

    $filesize = file_put_contents(filename: $targetFile, data: $content);

    if ($filesize === false)
    {
      Logger::error("Could not create $targetFileRelative", exit: true);
    }

    Logger::logCreate(path: $targetFileRelative, filesize: $filesize);

    $modulePath = $featureDirectory . "/${name}Module.php";

    if (file_exists($modulePath))
    {
      WorkspaceManager::updateModule(moduleName: $name, targetArray: 'controllers', newEntry: "${name}Controller");
    }
  }

  /**
   * Build an entity declaration
   * 
   * @param null|string $path The path to the new entity.
   * @param null|string $featureName The name of the feature to which the entity 
   * belongs (if it has one).
   */
  public function buildEntity(?string $path, ?string $featureName = null): void
  {
    if (empty($path))
    {
      $path = prompt('Entity path', attempts: 3);
    }

    $isPartOfFeature = !empty($featureName);

    if ($isPartOfFeature)
    {
      $path = "$featureName/$path";
    }

    $defaultTableName = !empty($featureName) ? null : pascalToSnake(basename($path));

    $tableName = $featureName ?? prompt('Which database table does the entity represent', defaultValue: $defaultTableName);

    $templatePath = $this->templateDirectory() . "/Entity.Template.php";
    $modulesDirectory = $this->modulesDirectory();
    $targetFile = "$modulesDirectory/${path}Entity.php";
    $targetFileRelative = $this->relativeWorkingDirectoryPath(path: $targetFile);

    if (!file_exists($templatePath))
    {
      Logger::error("Missing schematic template.");
    }

    if (file_exists($targetFile))
    {
      Logger::error("$targetFileRelative already exists!", exit: true);
    }
    $content = file_get_contents($templatePath);
    $content = str_replace('/', '\\', str_replace('ModuleName', dirname($path), $content));
    $content = str_replace('/', '\\', str_replace('ClassName', basename($path), $content));
    $content = str_replace('TableName', pascalToSnake($tableName), $content);

    $filesize = file_put_contents(filename: $targetFile, data: $content);

    if ($filesize === false)
    {
      Logger::error("Could not create $targetFileRelative", exit: true);
    }

    Logger::logCreate(path: $targetFileRelative, filesize: $filesize);

    if ($isPartOfFeature)
    {
      $targetDirectory = dirname($targetFile);
      $moduleFiles = array_slice(scandir($targetDirectory), 2);

      $repositories = preg_grep("/repository\.php$/i", $moduleFiles);

      foreach ($repositories as $repositoryFilename)
      {
        $repositoryPath = $targetDirectory . DIRECTORY_SEPARATOR . $repositoryFilename;
        $repositoryPathRelative = $this->relativeWorkingDirectoryPath(path: $repositoryPath);

        if (!file_exists($repositoryPath))
        {
          Logger::error("Could not filnd $repositoryPath", exit: true);
        }

        $fileContent = file_get_contents($repositoryPath);
        $entityName = basename($path, '.php');
        $fileContent = str_replace("entity: ''", sprintf("entity: %sEntity::class", $entityName), $fileContent);

        $filesize = file_put_contents($repositoryPath, $fileContent);

        if ($filesize === false)
        {
          Logger::error("Could not create $repositoryFilename", exit: true);
        }

        Logger::logUpdate(path: $repositoryPathRelative, filesize: $filesize);
      }
    }
  }

  /**
   * Build an enumeration declaration
   * 
   * @param null|string $name The name of the enumeration.
   */
  public function buildEnum(?string $name): void
  {
    // TODO: #47 feat(lib): implement enum schematic
    throw new NotImplementedException();
  }

  /**
   * Build a feature declaration
   * 
   * @param null|string $name The name of the feature.
   */
  public function buildResource(?string $name): void
  {
    if (empty($name))
    {
      $name = prompt(sprintf("What's the name of the feature", Color::LIGHT_GREEN, Color::RESET), attempts: 3);
    }
    $this->buildModule(name: $name);
    
    $this->buildController(name: $name);
    
    $this->buildService(name: $name);
    
    echo "\n";
    if (confirm(message: "Would you like to generate a repository"))
    {
      $this->buildRepository(name: $name);
    }
    
    echo "\n";
    if (confirm(message: "Would you like to generate an entity"))
    {
      $entityName = prompt(message: 'What would you like to call the Entity', attempts: 3);
      $this->buildEntity(path: $entityName, featureName: $name);
    }
  }

  /**
   * Build a guard declaration
   * 
   * @param null|string $name The name of the guard.
   */
  public function buildGuard(?string $path, ?string $featureName = null): void
  {
    if (empty($path))
    {
      $path = prompt('Guard path', attempts: 3);
    }

    if (!empty($featureName))
    {
      $path = "$featureName/$path";
    }

    $guardName = basename(path: $path);

    $templatePath = $this->templateDirectory() . "/Guard.Template.php";
    $projectPath = $this->getWorkingDirectory();
    $targetFile = "$projectPath/app/src/${path}Guard.php";
    $targetFileRelative = $this->relativeWorkingDirectoryPath(path: $targetFile);
    $targetDirectory = dirname($targetFile);
    $targetDirectoryRelative = $this->relativeWorkingDirectoryPath(path: $targetDirectory);

    if (!file_exists($templatePath))
    {
      Logger::error("Missing schematic template.");
    }

    if (file_exists($targetFile))
    {
      Logger::error("$targetFileRelative already exists!", exit: true);
    }

    if (!file_exists($targetDirectory))
    {
      if (!mkdir(directory: $targetDirectory, recursive: true))
      {
        Logger::error("Could not create " . $targetDirectoryRelative, exit: true);
      }

      if ($this->verbose)
      {
        Logger::logCreate($targetDirectoryRelative);
      }
    }

    if (file_exists($targetFile))
    {
      Logger::error("$targetFileRelative already exists!", exit: true);
    }
    $content = file_get_contents($templatePath);
    $content = str_replace('/', '\\', str_replace('GuardNamespace', dirname($path), $content));
    $content = str_replace('/', '\\', str_replace('GuardName', $guardName, $content));

    $filesize = file_put_contents(filename: $targetFile, data: $content);

    if ($filesize === false)
    {
      Logger::error("Could not create $targetFileRelative", exit: true);
    }

    Logger::logCreate(path: $targetFileRelative, filesize: $filesize);
  }

  /**
   * Build a module declaration
   * 
   * @param null|string $name The name of the module.
   */
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
      if (!mkdir(directory: $modulesDirectory, recursive: true))
      {
        Logger::error("Could not create modules directory.", exit: true);
      }

      if ($this->verbose)
      {
        Logger::logCreate('app/src/Modules');
      }
    }

    if (!file_exists($featureDirectory))
    {
      if (!mkdir(directory: $featureDirectory, recursive: true))
      {
        Logger::error("Could not create " . $featureDirectoryRelative, exit: true);
      }

      if ($this->verbose)
      {
        Logger::logCreate($featureDirectoryRelative);
      }
    }

    if (file_exists($targetFile))
    {
      Logger::error("$targetFileRelative already exists!", exit: true);
    }
    $content = file_get_contents($templatePath);
    $content = str_replace('ModuleName', $name, $content);

    $filesize = file_put_contents(filename: $targetFile, data: $content);

    if ($filesize === false)
    {
      Logger::error("Could not create $targetFileRelative", exit: true);
    }

    Logger::logCreate(path: $targetFileRelative, filesize: $filesize);

    $modulePath = $featureDirectory . "/${name}Module.php";

    if (file_exists($modulePath))
    {
      WorkspaceManager::updateRoutes(moduleName: $name);
    }
  }

  /**
   * Build a repository declaration
   * 
   * @param null|string $name The name of the repository.
   */
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
      if (!mkdir(directory: $modulesDirectory, recursive: true))
      {
        Logger::error("Could not create modules directory.", exit: true);
      }

      if ($this->verbose)
      {
        Logger::logCreate('app/src/Modules');
      }
    }

    if (!file_exists($featureDirectory))
    {
      if (!mkdir(directory: $featureDirectory, recursive: true))
      {
        Logger::error("Could not create " . $featureDirectoryRelative, exit: true);
      }

      if ($this->verbose)
      {
        Logger::logCreate($featureDirectoryRelative);
      }
    }

    if (file_exists($targetFile))
    {
      Logger::error("$targetFileRelative already exists!", exit: true);
    }
    $content = file_get_contents($templatePath);
    $content = str_replace('ModuleName', $name, $content);
    $content = str_replace('TableName', pascalToSnake($name), $content);

    $filesize = file_put_contents(filename: $targetFile, data: $content);

    if ($filesize === false)
    {
      Logger::error("Could not create $targetFileRelative", exit: true);
    }

    Logger::logCreate(path: $targetFileRelative, filesize: $filesize);

    $modulePath = $featureDirectory . "/${name}Module.php";

    if (file_exists($modulePath))
    {
      WorkspaceManager::updateModule(moduleName: $name, targetArray: 'providers', newEntry: "${name}Repository");
    }
  }

  /**
   * Build a service declaration
   * 
   * @param null|string $name The name of the service.
   */
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
      if (!mkdir(directory: $modulesDirectory, recursive: true))
      {
        Logger::error("Could not create modules directory.", exit: true);
      }

      if ($this->verbose)
      {
        Logger::logCreate('app/src/Modules');
      }
    }

    if (!file_exists($featureDirectory))
    {
      if (!mkdir(directory: $featureDirectory, recursive: true))
      {
        Logger::error("Could not create " . $featureDirectoryRelative, exit: true);
      }

      if ($this->verbose)
      {
        Logger::logCreate($featureDirectoryRelative);
      }
    }

    if (file_exists($targetFile))
    {
      Logger::error("$targetFileRelative already exists!", exit: true);
    }
    $content = file_get_contents($templatePath);
    $content = str_replace('ModuleName', $name, $content);

    $filesize = file_put_contents(filename: $targetFile, data: $content);

    if ($filesize === false)
    {
      Logger::error("Could not create $targetFileRelative", exit: true);
    }

    Logger::logCreate(path: $targetFileRelative, filesize: $filesize);

    $modulePath = $featureDirectory . "/${name}Module.php";

    if (file_exists($modulePath))
    {
      WorkspaceManager::updateModule(moduleName: $name, targetArray: 'providers', newEntry: "${name}Service");
    }
  }

  /**
   * Outputs the schematic templates directory path.
   * 
   * @return string Returns the schematic templates directory path.
   */
  public function templateDirectory(): string
  {
    global $assegaiPath;
    return sprintf("%s/templates/schematics", $assegaiPath);
  }

  /**
   * Outputs the project modules directory path.
   * 
   * @return string Returns the project modules directory path.
   */
  public function modulesDirectory(): string
  {
    global $workingDirectory;
    return sprintf("%s/app/src/Modules", $workingDirectory);
  }

  /**
   * Outputs the current working directory absolute path.
   * 
   * @return string Returns the current working directory absolute path.
   */
  public function getWorkingDirectory(): string
  {
    global $workingDirectory;
    return $workingDirectory;
  }

  /**
   * Outputs the current working directory relative path.
   * 
   * @return string Returns the current working directory relative path.
   */
  public function relativeWorkingDirectoryPath(string $path): string
  {
    global $workingDirectory;
    return preg_replace('/^\//', '', str_replace($workingDirectory, '', $path));
  }
}