#!/usr/bin/env php
<?php

use Assegai\CLI\LIB\Color;

require_once 'bootstrap.php';

$systemInfo = [
  'OS Version'        => PHP_OS_FAMILY,
  'PHP Version'       => PHP_VERSION,
  'Composer Version'  => str_replace('Composer version ', '', exec('composer -V')),
];

$assegaiCLI = [
  'Assegai CLI Version' => getVersion(),
  'Assegai Version' => getFrameworkVersion(),
];

// $assegaiPlatformInfo = [];

printHeader();

printf("\n%s[System Information]%s\n", Color::GREEN, Color::RESET);
foreach ($systemInfo as $key => $value)
{
  printf("%-25s: %s%s%s\n", $key, Color::LIGHT_BLUE, $value, Color::RESET);
}

printf("\n%s[Assegai CLI]%s\n", Color::GREEN, Color::RESET);
foreach ($assegaiCLI as $key => $value)
{
  printf("%-25s: %s%s%s\n", $key, Color::LIGHT_BLUE, $value, Color::RESET);
}

// printf("%s[Assegai Platform Information]%s\n", Color::LIGHT_GREEN, Color::RESET);
echo "\n";