#!/usr/bin/php
<?php

namespace Assegai\LIB;

use Assegai\LIB\Logging\Logger;
use Assegai\LIB\Menus\Menu;
use Assegai\LIB\Menus\MenuItem;
use PDO;

final class DatabaseSelector
{
  const CONFIG_FILE_PATH        = 'app/config/default.php';
  const LOCAL_CONFIG_FILE_PATH  = 'app/config/local.php';
  const PROD_CONFIG_FILE_PATH   = 'app/config/production.php';

  private ?PDO $connection = null;

  private string $message = '';
  private array $config = [];
  private array $availableDatabases = [];
  private ?string $selectedDatabase = null;
  private ?Menu $databaseTypesMenu = null;
  private ?Menu $availableDatabasesMenu = null;

  public function __construct(
    private ?string $databaseType = null,
    private ?string $databaseName = null,
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
      exit("\e[1;31mMissing file: " . self::CONFIG_FILE_PATH . "\e[0m\n");
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
    $this->connection = DBFactory::getSQLConnection(config: $this->config, dialect: $this->databaseType());
  }

  private function isQuitRequest(string $input): bool
  {
    return in_array(strtolower($input), ['x', 'quit', 'exit', 'kill', 'stop']);
  }
}