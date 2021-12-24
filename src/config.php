#!/usr/bin/env php
<?php

use Assegai\CLI\LIB\Logging\Logger;

require_once 'bootstrap.php';

checkWorkspace(commandName: 'config');

$configPath = "$workingDirectory/assegai.json";

if (!file_exists($configPath))
{
  Logger::error(message: 'Missing config file!', exit: true);
}

$config = file_get_contents($configPath);

if (is_string($config) && preg_match('/^[{\[]/', $config))
{
  if (count($args) === 1)
  {
    echo jsonPrettify(json: $config);
  }
  else
  {
    $newConfig = $config;
    $config = json_decode($config);
 
    list($command, $path, $newValue) = match(count($args)) {
      1       => [$args[0], null,     null],
      2       => [$args[0], $args[1], null],
      default => $args
    };
    $path = explode('.', $path);

    if (is_array($path) && !empty($path))
    {
      $handle = $config;

      foreach ($path as $index => $token)
      {
        if (!empty($token) && isset($handle->$token))
        {
          $previousHanlde = $handle;
          $handle = $handle->$token;
          if (!is_null($newValue))
          {
            $previousHanlde->$token = $newValue;
            $needle = match(gettype($previousHanlde)) {
              'object',
              'array', => json_encode($previousHanlde),
              default => $previousHanlde
            };
            $newConfig = str_replace($needle, json_encode($handle), $newConfig);

            exit($newConfig);
          }
        }
      }

      $response = match(gettype($handle)) {
        'object',
        'array' => json_encode($handle),
        default => $handle 
      } ?? null;

      if (!empty($response))
      {
        echo match(substr($response, 0, 1)) {
          '{',
          '['     => str_replace("\n\n", "\n", jsonPrettify(json: $response) . "\n"),
          default => "$response\n"
        };
      }
    }
  }
}
