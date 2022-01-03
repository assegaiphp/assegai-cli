#!/usr/bin/php
<?php

require_once 'bootstrap.php';

list($command) = match (count($args)) {
  1 => [null],
  default => array_slice($args, 1)
};

if (empty($command))
{
  printHeader();
  printf("%s\n", $mainMenu);
}
else if (!$mainMenu->hasItemWithValue(valueOrAlias: $command))
{
  printHeader();
  printf("%s\n", $mainMenu);
}
else
{
  $mainMenu->describeItem(itemValueOrIndex: $command);
}