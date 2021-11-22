#!/usr/bin/php
<?php

use Assegai\CLI\LIB\Logging\Logger;

require_once 'bootstrap.php';

list($command, $requiredArg, $optionalArg, $option) = match (count($args)) {
  1       => [$args[0], null,     null,     null],
  2       => [$args[0], $args[1], null,     null],
  3       => [$args[0], $args[1], $arg[2],  null],
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
  if (!file_exists("$assegaiPath/src/$command.php"))
  {
    Logger::error(message: "Unknown command $command", terminateAfterLog: true);
  }

  require_once "$assegaiPath/src/$command.php";
}