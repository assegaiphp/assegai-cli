#!/usr/bin/php
<?php

namespace Assegai\LIB;

use Assegai\LIB\Logging\Logger;

final class WorkspaceManager
{
  public static function isAssegaiWorkspace(): bool
  {
    global $workingDirectory;
    $configFile = sprintf("%s/assegai.json", $workingDirectory);
    return file_exists($configFile);
  }

  public static function updateModule(string $moduleName, string $targetArray, string $newEntry): void
  {
    global $workingDirectory;
    $moduleFilePath = sprintf("%s/app/src/Modules/%s/%sModule.php", $workingDirectory, $moduleName, $moduleName);
    
    if (file_exists($moduleFilePath))
    {
      $lines = file($moduleFilePath);

      $startIndex = null;
      $endIndex = null;
      $startsAndEndsOnSameLine = false;
      $targetArray = strtolower($targetArray);
      $output = [];

      foreach ($lines as $index => $line)
      {
        if (str_contains($line, "$targetArray:"))
        {
          $startIndex = $index;
          if (preg_match("/\],\n\r*$/", $line))
          {
            $lines[$index] =
              str_contains($line, "$targetArray: []")
              ? str_replace("$targetArray: []", "$targetArray: [${newEntry}::class]", $line)
              : str_replace("],", ", ${newEntry}::class],", $line);
          }
        }

        if (is_numeric($startIndex) && is_null($endIndex))
        {
          if (str_ends_with($line, "],\n"))
          {
            $endIndex = $index;
          }
        }
      }

      $startsAndEndsOnSameLine = $startIndex === $endIndex;
      $output = "";

      if (is_numeric($endIndex) && !$startsAndEndsOnSameLine)
      {
        if (!str_ends_with($lines[$endIndex - 1], ",\n"))
        {
          $lines[$endIndex - 1] = str_replace("\n", ",\n", $lines[$endIndex - 1]);
        }
        $lines = array_merge(
          array_slice($lines, 0, $endIndex),
          ["    ${newEntry}::class,"],
          array_slice($lines, $endIndex)
        );
      }


      foreach ($lines as $line)
      {
        $output .= trim($line, "\n\r") . "\n";
      }

      $bytes = file_put_contents($moduleFilePath, trim($output));

      echo "\e[1;34mUPDATE\e[0m ${moduleFilePath} ($bytes bytes)\n";
    }
  }

  public static function updateRoutes(string $moduleName): void
  {
    global $workingDirectory;
    $routesFilePath = sprintf("%s/app/routes.php", $workingDirectory);
    $namespace = "use Assegai\\Modules\\${moduleName}\\${moduleName}Module;";
    $path = strtolower($moduleName);
    $class = "${moduleName}Module::class";
    $route = "  'new Route(path: '${path}', module: ${class}),";

    $lines = file($routesFilePath);

    $lastNamespaceIndex = 2;
    $lastRouteIndex = 6;
    $foundNamespace = false;
    $foundRoute = false;

    foreach ($lines as $index => $line)
    {
      if (str_ends_with($line, "Module;\n"))
      {
        $lastNamespaceIndex = $index + 1;
      }
      if (str_contains($line, $namespace))
      {
        $foundNamespace = true;
      }
      if (str_contains($line, $route))
      {
        $foundRoute = true;
      }
      if (str_ends_with($line, "::class),\n"))
      {
        $lastRouteIndex = $index;
      }
    }

    $output = [];
    if (!$foundNamespace)
    {
      # Add namespace
      $output = array_merge(
        array_slice($lines, 0, $lastNamespaceIndex),
        [$namespace],
        array_slice($lines, $lastNamespaceIndex),
      );
    }

    if (!$foundRoute)
    {
      $prefixArray = empty($output)
        ? array_slice($lines, 0, $lastNamespaceIndex + 1)
        : array_slice($output, 0, $lastNamespaceIndex + 1);
      # Add route
      $output = array_merge(
        $prefixArray,
        array_slice($lines, $lastNamespaceIndex, ($lastRouteIndex - $lastNamespaceIndex) + 1),
        [$route],
        array_slice($lines, ($lastRouteIndex + 1)),
      );
    }

    $lines = "";

    if (empty($output))
    {
      exit;
    }

    foreach ($output as $line)
    {
      $lines .= trim($line, "\n\r") . "\n";
    }

    $bytes = file_put_contents($routesFilePath, trim($lines));

    Logger::logUpdate(sprintf("app/routes.php (%d bytes)", $bytes));
  }
}