#!/usr/bin/php
<?php

namespace Assegai\LIB;

use Assegai\LIB\Logging\Logger;
use Assegai\LIB\Menus\Menu;
use Assegai\LIB\Menus\MenuItem;
use PDO;
use PDOException;

final class DatabaseSelector
{
  const CONFIG_FILE_PATH        = 'app/config/default.php';
  const LOCAL_CONFIG_FILE_PATH  = 'app/config/local.php';
  const PROD_CONFIG_FILE_PATH   = 'app/config/production.php';

  private ?PDO $connection = null;

  private array $config = [];
  private array $availableDatabases = [];
  private ?Menu $databaseTypesMenu = null;
  private ?Menu $availableDatabasesMenu = null;

  public function __construct(
    private ?string $databaseType = null,
    private ?string $databaseName = null,
    private bool $promptToCreate = false
  )
  {
    $this->databaseTypesMenu = new Menu(title: 'Database Types:');
    $this->availableDatabasesMenu = new Menu(title: 'Available Databases:');
  }

  public function databaseType(): ?string { return $this->databaseType; }

  public function databaseName(): ?string { return $this->databaseName; }

  public function config(): array { return $this->config; }

  public function connection(): ?PDO { return $this->connection; }

  public function run(): void
  {
    global $workingDirectory;

    if (!file_exists(self::CONFIG_FILE_PATH))
    {
      Logger::error("Missing file: " . self::CONFIG_FILE_PATH, terminateAfterLog: true);
    }

    $this->config = require(self::CONFIG_FILE_PATH);

    if (file_exists("$workingDirectory/" . self::LOCAL_CONFIG_FILE_PATH))
    {
      $localConfig = require("$workingDirectory/" . self::LOCAL_CONFIG_FILE_PATH);
      if (is_array($localConfig))
      {
        $this->config = array_merge($this->config, $localConfig);
      }
    }

    if (!isset($this->config['databases']))
    {
      Logger::error("Invalid config: 'databases' is not defined.", terminateAfterLog: true);
    }

    # 1. Select Database Type
    if (is_null($this->databaseType()))
    {
      $availableDatabases = array_keys($this->config['databases']);
  
      foreach ($availableDatabases as $index => $db)
      {
        $this->databaseTypesMenu->add(new MenuItem(value: $db));
      }
      $this->databaseTypesMenu->add(new MenuItem(value: 'quit', index: 'x', indexColor: 'red'));
      $choice = $this->databaseTypesMenu->prompt();
  
      if (is_null($choice))
      {
        Logger::error('Invalid choice', terminateAfterLog: true);
      }
  
      if ($this->isQuitRequest(input: $choice->value()))
      {
        exit(0);
      }
      $this->databaseType = $choice->value();
    }

    # 2. Select Database
    if (is_null($this->databaseName()))
    {
      $this->availableDatabases = array_keys($this->config['databases'][$this->databaseType()]); 
  
      foreach ($this->availableDatabases as $index => $db)
      {
        $this->availableDatabasesMenu->add(new MenuItem(value: $db));
      }
      $this->availableDatabasesMenu->add(new MenuItem(value: 'quit', index: 'x', indexColor: 'red'));
      $choice = $this->availableDatabasesMenu->prompt();
      echo "\n";
  
      if (is_null($choice))
      {
        Logger::error('Invalid choice');
      }
  
      if ($this->isQuitRequest(input: $choice->value()))
      {
        exit(0);
      }
  
      if (!isset($this->config['databases'][$this->databaseType()][$choice->value()]))
      {
        Logger::error('Invalid choice');
      }

      $this->databaseName = $this->config['databases'][$this->databaseType()][$choice->value()]['name'] ?? null;
    }

    # 3. Establish Database connection
    if (!isset($this->config['databases'][$this->databaseType()]))
    {
      Logger::error('Unknown database type: ' . $this->databaseType(), terminateAfterLog: true);
    }

    if (!isset($this->config['databases'][$this->databaseType()][$this->databaseName()]))
    {
      Logger::error('Unknown database name: ' . $this->databaseName(), terminateAfterLog: true);
    }

    $this->config = $this->config['databases'][$this->databaseType()][$this->databaseName()];
    $this->config['dontExit'] = true;
    $this->connection = DBFactory::getSQLConnection(config: $this->config, dialect: $this->databaseType());

    if (!empty(DBFactory::errors()))
    {
      # Search for database doesn't exist error(1049)
      foreach (DBFactory::errors() as $index => $error)
      {
        if ($index === 1049)
        {
          printf("Unknown database '%s%s%s'.\n\n", Color::YELLOW, $this->databaseName(), Color::RESET);
          if ($this->promptToCreate)
          {
            $answer = $this->readLine(message: 'Would you like to create it? ', suffix: '[Y/n]', defaultValue: 'y');
            $answer = match(strtolower($answer)) {
              'y',
              'yes',
              'yep',
              'yeah'  => 'yes',
              default => 'no'
            };

            if ($answer === 'no')
            {
              Logger::warn('Database not defined. Terminating program.', terminateAfterLog: true);
            }
            else
            {
              try
              {
                $statement = $this->connection()->query(sprintf("CREATE DATABASE IF NOT EXISTS `%s`", $this->databaseName()));

                if ($statement === false)
                {
                  Logger::error(implode("\n", $this->connection()->errorInfo()), terminateAfterLog: true);
                }

                Logger::logCreate($this->databaseName() . ' database');
              }
              catch(PDOException $e)
              {
                Logger::error($e->getMessage(), terminateAfterLog: true);
              }
            }
            break;
          }
        }
      }
    }

    unset($this->config['dontExit']);
  }

  private function readLine(
    string $message = '',
    string $suffix = '',
    ?string $defaultValue = null
  ): string
  {
    printf("%s: %s " . Color::BLUE, $message, $suffix);
    $line = trim(fgets(STDIN));
    echo Color::RESET;
    if (empty($line))
    {
      $line = $defaultValue;
    }
    return $line;
  }

  private function isQuitRequest(string $input): bool
  {
    return in_array(strtolower($input), ['x', 'quit', 'exit', 'kill', 'stop']);
  }
}