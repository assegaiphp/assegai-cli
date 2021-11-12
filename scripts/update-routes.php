<?php

if ($argc > 1)
{
  $routesFilePath = 'app/routes.php';
  $moduleName = $argv[1];
  $namespace = "use LifeRaft\\Modules\\${moduleName}\\${moduleName}Module;";
  $path = strtolower($moduleName);
  $class = "${moduleName}Module::class";
  $route = "  '$path' => ${class},";

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
    if (str_ends_with($line, "::class,\n"))
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

  echo "\e[1;34mUPDATE\e[0m app/routes.php ($bytes bytes)\n";
}

?>