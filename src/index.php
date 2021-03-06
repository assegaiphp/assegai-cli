#!/usr/bin/php
<?php

use Assegai\CLI\LIB\Logging\Logger;

require_once 'bootstrap.php';
require_once $assegaiPath . '/vendor/autoload.php';

list($command, $requiredArg, $optionalArg, $option) = match (count($args)) {
  0       => [null,     null,     null,     null],
  1       => [$args[0], null,     null,     null],
  2       => [$args[0], $args[1], null,     null],
  3       => [$args[0], $args[1], $args[2],  null],
  default => $args
};

if (empty($command) || !$mainMenu->hasItemWithValue(valueOrAlias: $command))
{
  printHeader();
  printf("%s\n", $mainMenu);
}
else if (in_array($requiredArg, ['--help', '-h']))
{
  $mainMenu->describeItem(itemValueOrIndex: $command);
}
else
{
  $command = $mainMenu->getItemValue(valueOrAlias: $command);
  
  if (!file_exists("$assegaiPath/src/$command.php"))
  {
    Logger::error(message: "Unknown command $command", exit: true);
  }

  require_once "$assegaiPath/src/$command.php";
}