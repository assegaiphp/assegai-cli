#!/usr/bin/php
<?php

namespace Assegai\LIB\Migration;

use Assegai\LIB\Color;
use Assegai\LIB\Logging\Logger;
use PDO;
use PDOException;

/**
 * The `Migrator` class provides methods for maintaining database schema.
 * 
 * A database migration always provides procedures to update the schema, 
 * as well as to revert itself. Assegai's migrations are versioned, and 
 * run in order. Assegai also takes care of tracking which migrations have 
 * already been run automatically. Your migrations don't need to be 
 * idempotent, as Assegai will ensure no migration is run twice unless it 
 * has been reverted.
 * 
 * @author A. Masiye <amasiye313@gmail.com>
 */
final class Migrator
{
  const RUN_ALL     = 'run_all';
  const RUN_NEXT    = 'run_next';
  const REVERT_ALL  = 'revert_all';
  const REVERT_NEXT = 'revert_next';

  private string $databaseType = 'mysql';
  private string $tableName = '';

  public function __construct(
    private PDO $connection,
    private string $migrationDirectory
  ) {
    $dirTokens = explode(DIRECTORY_SEPARATOR, $this->migrationDirectory);
    list($this->databaseType, $this->tableName) = array_splice($dirTokens, -2);
  }

  public function generate(string $name): void
  {
    $name = strtolower($name);
    $name = preg_replace('/([\W]+)/', '', preg_replace('/([-\s]+)/', '_', $name));

    $timestamp = date('Ymdhis');
    $name = "${timestamp}_${name}";
    $dirname = $this->migrationDirectory . "/$name";
    $upFilename = "$dirname/up.sql";
    $downFilename = "$dirname/down.sql";

    if (!file_exists($dirname))
    {
      if (!mkdir(directory: $dirname, recursive: true))
      {
        Logger::error(message: "Failed to create $dirname", terminateAfterLog: true);
      }

      Logger::logCreate(path: $dirname);
    }

    if (!file_exists($upFilename))
    {
      if (!fopen(filename: $upFilename, mode: 'w'))
      {
        Logger::error(message: "Failed to create $upFilename", terminateAfterLog: true);
      }

      Logger::logCreate(path: $upFilename);
    }

    if (!file_exists($downFilename))
    {
      if (!fopen(filename: $downFilename, mode: 'w'))
      {
        Logger::error(message: "Failed to create $downFilename", terminateAfterLog: true);
      }

      Logger::logCreate(path: $downFilename);
    }
  }

  public function run(string $mode = self::RUN_ALL): void
  {
    $runCount = 0;
    $migrations = $this->listMigrations();

    try
    {
      $ranMigrations = [];
      foreach ($migrations as $timestamp => $migration)
      {
        ++$runCount;
        if (is_null($migration->ranOn()))
        {
          # Run the up.sql file
          $upFilename = sprintf("%s/%s/up.sql", $this->migrationDirectory, $migration->name());
          $contents = file_get_contents($upFilename);
          $statement = $this->connection->query($contents);

          if ($statement === false)
          {
            Logger::error(message: sprintf("Failed to run %s", $migration->name()), terminateAfterLog: true);
          }

          $ranMigrations[$migration->value()] = $migration;

          # Record migration
          $migrationValue = $migration->value();
          $recordStatement = "INSERT INTO `__assegai_schema_migrations` (`migration`) VALUES('$migrationValue')";
          $recordStatement = $this->connection->query($recordStatement);
          if ($statement === false)
          {
            Logger::error(message: implode("\n", $this->connection->errorInfo()), terminateAfterLog: true);
          }
        }
      }

      if (empty($ranMigrations))
      {
        Logger::log(message: 'Nothing to do', terminateAfterLog: true);
      }
      Logger::logUpdate(path: '__assegai_schema_migrations');
    }
    catch(PDOException $e)
    {
      Logger::error($e->getMessage(), terminateAfterLog: true);
    }
  }

  public function revert(string $mode = self::RUN_ALL): void
  {

  }

  public function redo(): void
  {
    $this->revert(mode: self::REVERT_NEXT);
    $this->run(mode: self::RUN_NEXT);
  }

  public function listMigrations(): array|false
  {
    if (file_exists($this->migrationDirectory))
    {
      $allMigrations = scandir($this->migrationDirectory);
      $allMigrations = array_splice($allMigrations, 2);
      $alreadyRan = $this->getRanMigrations();
      $titleColor = Color::YELLOW;
      $resetColor = Color::RESET;

      echo "\n${titleColor}Migrations:${resetColor}\n";
      
      foreach ($allMigrations as $item)
      {
        $key = substr($item, 0, 14);
        $list[$key] = new Migration(name: $item);
      }

      foreach ($alreadyRan as $item)
      {
        if (isset($list[$item['migration']]))
        {
          $list[$item['migration']]->setRanOn($item['ran_on']);
        }
      }

      return $list;
    }
    else
    {
      return false;
    }
  }

  private function getRanMigrations(): array
  {
    $queryString = match($this->databaseType) {
      default => "SELECT * FROM __assegai_schema_migrations"
    };

    $result = $this->connection->query($queryString);

    if ($result === false)
    {
      Logger::error(implode("\n", $this->connection->errorInfo()), terminateAfterLog: true);
    }

    return $result->fetchAll(mode: PDO::FETCH_ASSOC);
  }
}