#!/usr/bin/env php
<?php

use Assegai\CLI\LIB\Color;

require_once 'bootstrap.php';

if (file_exists("$workingDirectory/run-unittest"))
{
  printf("%sRunning Unit Tests...%s\n\n", Color::BLUE, Color::RESET);
  echo shell_exec('./run-unittest') . "\n";
}