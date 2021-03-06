#!/usr/bin/env php
<?php

use Assegai\CLI\LIB\Color;
use Assegai\CLI\LIB\Logging\Logger;
use Assegai\CLI\LIB\Util\Config;

array_shift($args);
list($path) = match(count($args)) {
  0       => [null],
  default => $args
};

$config = Config::get(path: $path);
$host = 'localhost';
$port = '3000';
$routerPath = "assegai-router.php";

if (empty($config))
{
  exit;
}

if (
  isset($config->development) &&
  isset($config->development->server)
)
{
  $server = $config->development->server;
  if (isset($server->host))
  {
    $host = $server->host;
  }

  if (isset($server->port))
  {
    $port = $server->port;
  }

  if (isset($server->router))
  {
    $routerPath = $server->router;
  }
}

$router = file_exists("$workingDirectory/$routerPath") ? " $routerPath" : "";

$command = "php -S $host:${port}${router}";

Logger::log(message: sprintf("Starting Server...\n%sListening on port %s\n", Color::YELLOW, $port));
$response = exec($command);