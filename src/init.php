#!/usr/bin/env php
<?php

use Assegai\CLI\LIB\Logging\Logger;

require_once 'bootstrap.php';

$shortOptions = '';

$longOptions = [
  'name:',
  'description:',
  'version:',
  'project_type:',
  'root:',
  'source_root:',
  'prefix:',
  'scripts:',
  'development:',
];
$restIndex = 2;

$name = null;
$description = null;
$version = null;
$project_type = null;
$root = '';
$source_root = 'src';
$prefix = 'app';
$scripts = [
  'test' => './vendor/bin/phpunit --testdox'
];
$development = [
  'server' => [
    'host' => 'localhost',
    'port' => 5000,
  ]
];

$options = getoptions(shortOptions: $shortOptions, longOptions: $longOptions, restIndex: $restIndex, offset: 1);
extract($options);

if (!$name)
{
  $name = prompt(message: 'Project name', defaultValue: 'assegai-app');
}

if (!$description)
{
  $description = prompt(message: 'Description');
}

if (!$version)
{
  $version = prompt(message: 'Version', defaultValue: '0.0.0');
}

if (!$project_type)
{
  $project_type = prompt(message: 'Project Type', defaultValue: 'application');
}

$filepath = exec('pwd');
$sourceFilename = "$assegaiPath/templates/init/assegai.template";

if (!file_exists($sourceFilename))
{
  Logger::error(message: "File not found: " . basename($sourceFilename), exit: true);
}

$templateContent = file_get_contents($sourceFilename);
$assegaiJson = str_replace('NAME', $name, $templateContent);
$assegaiJson = str_replace('DESCRIPTION', $description, $assegaiJson);
$assegaiJson = str_replace('VERSION', $version, $assegaiJson);
$assegaiJson = str_replace('PROJECT_TYPE', $project_type, $assegaiJson);
$assegaiJson = str_replace('ROOT', $root, $assegaiJson);
$assegaiJson = str_replace('SOURCE', $source_root, $assegaiJson);

$filename = "$filepath/assegai.json";
$bytes = file_put_contents($filename, $assegaiJson);

if ($bytes === false)
{
  Logger::error(message: 'Failed to created ' . basename($bytes), exit: true);
}

echo "\n";
Logger::logCreate(path: basename($filename), filesize: $bytes);
