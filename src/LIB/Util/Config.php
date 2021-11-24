#!/usr/bin/env php
<?php

namespace Assegai\CLI\LIB\Util;

use Assegai\CLI\LIB\Logging\Logger;
use stdClass;

final class Config
{
  public static function get(?string $path = null): mixed
  {
    global $assegaiConfig;

    if (!file_exists($assegaiConfig))
    {
      Logger::error(message: "Missing config file!", terminateAfterLog: true);
    }

    $configFileContent = file_get_contents($assegaiConfig);

    if (empty($configFileContent))
    {
      Logger::error(message: "Empty config file!", terminateAfterLog: true);
    }

    if (!str_starts_with($configFileContent, "{") && !str_starts_with($configFileContent, "["))
    {
      Logger::error(message: "Invalid config file", terminateAfterLog: true);
    }

    $config = json_decode($configFileContent);
    $path = explode(separator: '.', string: $path);

    foreach ($path as $token)
    {
      if (!isset($config->$token))
      {
        break;
      }
      $config = $config->$token;
    }

    return $config;
  }

  private static function parse(string $path): ?stdClass
  {
    return null;
  }
}