#!/usr/bin/php
<?php

namespace Assegai\LIB\Logging;

use Assegai\LIB\Color;
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

  public static function logCreate(string $path, ?int $filesize = null): void
  {
    Logger::logFileAction(action: Logger::FILE_CREATE, path: $path, filesize: $filesize);
  }

  public static function logUpdate(string $path, ?int $filesize = null): void
  {
    Logger::logFileAction(action: Logger::FILE_UPDATE, path: $path, filesize: $filesize);
  }

  public static function logDelete(string $path, ?int $filesize = null): void
  {
    Logger::logFileAction(action: Logger::FILE_DELETE, path: $path, filesize: $filesize);
  }

  private static function logFileAction(string $action = Logger::FILE_CREATE, string $path, ?int $filesize = null): void
  {
    $colorCode = match($action) {
      Logger::FILE_CREATE => Color::GREEN,
      Logger::FILE_DELETE => Color::RED,
      Logger::FILE_UPDATE => Color::BLUE,
      default             => Color::YELLOW
    };

    $bytes = bytes_format(bytes: $filesize);
    $suffix = is_null($filesize) ? '' : " ($bytes)";

    printf("%s%s%s %s%s\n", $colorCode, $action, Color::RESET, $path, $suffix);
  }
}