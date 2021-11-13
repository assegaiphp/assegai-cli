#!/usr/bin/php
<?php

namespace Assegai\LIB\Migration;

use Assegai\LIB\Logging\Logger;
use PDO;

final class Migrator
{
  public function __construct(
    private PDO $connection,
    private string $migrationDirectory
  ) { }

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

  public function run(): void
  {
    // $statement = $this->connection->query("INSERT INTO __migrations (`migration`) VALUES('$name')");

    // if ($statement === false)
    // {
    //   Logger::error(message: implode("\n", $this->connection->errorInfo()), terminateAfterLog: true);
    // }

    Logger::logUpdate(path: '__migrations');
  }

  public function revert(): void
  {

  }

  public function redo(): void
  {
    $this->revert();
    $this->run();
  }
}