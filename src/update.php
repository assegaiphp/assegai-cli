#!/usr/bin/env php
<?php

use Assegai\CLI\LIB\Color;
use Assegai\CLI\LIB\Logging\Logger;
use Assegai\CLI\LIB\TextStyle;

require_once 'bootstrap.php';

Logger::log(message: sprintf("%s%s▹▹▹▹▹%s Update in progress... ☕\n", Color::LIGHT_BLUE, TextStyle::BLINK, Color::RESET));

$result = shell_exec("composer update");

Logger::log(message: "\n✔️ Update complete! \n");