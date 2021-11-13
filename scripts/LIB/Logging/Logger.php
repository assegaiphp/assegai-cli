#!/usr/bin/php
<?php

namespace Assegai\LIB\Logging;

use Assegai\LIB\Logging\Log;

final class Logger
{
  const FILE_CREATE = 'CREATE';
  const FILE_UPDATE = 'UPDATE';
  const FILE_DELETE = 'DELETE';

  public static function log(string $message, bool $terminateAfterLog = false): void
  {
    echo new Log(message: "\e[1;34m${message}\e[0m\n");
    if ($terminateAfterLog)
    {
      exit(2);
    }
  }

  public static function warn(string $message, bool $terminateAfterLog = false): void
  {
    echo new Log(message: "\e[1;33m${message}\e[0m\n");
    if ($terminateAfterLog)
    {
      exit(2);
    }
  }

  public static function error(string $message, bool $terminateAfterLog = false): void
  {
    echo new Log(message: "\e[1;31m${message}\e[0m\n");
    if ($terminateAfterLog)
    {
      exit(2);
    }
  }

  public static function logCreate(string $path): void
  {
    Logger::logFileAction(action: Logger::FILE_CREATE, path: $path);
  }

  public static function logUpdate(string $path): void
  {
    Logger::logFileAction(action: Logger::FILE_UPDATE, path: $path);
  }

  public static function logDelete(string $path): void
  {
    Logger::logFileAction(action: Logger::FILE_DELETE, path: $path);
  }

  private static function logFileAction(string $action = Logger::FILE_CREATE, string $path): void
  {
    $colorCode = match($action) {
      Logger::FILE_CREATE => "\e[1;32m",
      Logger::FILE_DELETE => "\e[1;31m",
      Logger::FILE_UPDATE => "\e[1;34m",
      default => "\e[1;33m"
    };

    echo "${colorCode}${action}\e[0m $path\n";
  }
}