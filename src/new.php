#!/usr/bin/env php
<?php

use Assegai\CLI\LIB\Color;
use Assegai\CLI\LIB\Generation\SchematicBuilder;
use Assegai\CLI\LIB\Logging\Logger;
use Assegai\CLI\LIB\TextStyle;
use Assegai\CLI\LIB\Util\Console;
use Assegai\CLI\LIB\Util\TermInfo;
use Assegai\CLI\LIB\WorkspaceManager;

require_once 'bootstrap.php';

$repositoryURL="https://github.com/amasiye/assegai-php.git";

echo "⚡  We will scaffold your app in a few seconds...\n\n";

array_shift($args);
list($projectName) = match(count($args)) {
  0 => [null],
  default => $args,
};

if (empty($projectName))
{
  $projectName = prompt(message: "What name would you like to use for the new project?", defaultValue: 'assegai-app', attempts: 3);
}

$projectPath = "$workingDirectory/$projectName";
$composerPath = "$projectPath/composer.json";

if (!file_exists($projectPath))
{
  if (!mkdir(directory: $projectPath, recursive: true))
  {
    Logger::error("Couldn't not create project directory: ${projectName}", exit: true);
  }

  Logger::logCreate("$projectName");
}

# Copy project files
printf("%s%s▹▹▹▸▹%s Installation in progress... ☕%s\n", TextStyle::BLINK, Color::LIGHT_BLUE, Color::WHITE, Color::RESET);
$execOutput = [];
$resultCode = null;

# Initialize the project
$outuput = [];
WorkspaceManager::init(path: $projectPath, name: $projectName, output: $outuput);

# Copy files
$copyResult = exec(command: "cp -r $assegaiPath/templates/project/. $projectPath");

if ($copyResult === false)
{
  Logger::error(message: "Failed to copy project files.", exit: true);
}

if (!file_exists(filename: $composerPath))
{
  Logger::error(message: "File not found: $composerPath", exit: true);
}

# Configure composer
$composerJSON = file_get_contents($composerPath);
$safeName = $outuput['name'] ?? 'assegai_app';
$composerJSON = str_replace('NAME', "assegaiphp/$safeName", $composerJSON);
$composerJSON = str_replace('DESCRIPTION', $outuput['description'], $composerJSON);

$composerJSONUpdateResult = file_put_contents($composerPath, $composerJSON);

if ($composerJSONUpdateResult === false)
{
  Logger::error(message: "Failed to update $composerPath", exit: true);
}

Logger::log(message: "Installing dependencies...");

$dependancyInstallationResult = system(command: "cd $projectPath && composer update");

if ($dependancyInstallationResult === false)
{
  Logger::error(message: "Failed to install dependencies", exit: true);
}

$oldWorkingDirectory = $workingDirectory;
$workingDirectory = $projectPath;
$configPath = "$projectPath/app/config/default.php";

# Setup config
$configContent = file_get_contents($configPath);
$configContent = str_replace('NAME', $safeName, $configContent);
$configContent = str_replace('DESCRIPTION', $outuput['description'], $configContent);
$configContent = str_replace('VERSION', $outuput['version'], $configContent);

$configUpdateResult = file_put_contents($configPath, $configContent);

echo "\n";
$userServiceName = prompt(message: 'What is the name of the users feature?', defaultValue: 'Users');

if ($userServiceName)
{
  $schematicBuilder = new SchematicBuilder();
  $schematicBuilder->buildFeature(name: $userServiceName);
}

printf(
  "%s%s\r%s✔%s Installation done! ☕\n\n",
  Console::cursor()::moveUp(return: true),
  Console::eraser()::entireLine(),
  Color::LIGHT_GREEN,
  Color::RESET);

printf("🚀  Successfully created project %s%s%s\n", Color::LIGHT_GREEN, $projectName, Color::RESET);
printf("👉  Get started with the following commands:\n\n");
printf("%s$ cd %s%s\n", Color::DARK_WHITE, $projectName, Color::RESET);
printf("%s$ assegai serve %s\n\n\n", Color::DARK_WHITE, Color::RESET);

$thankYouMessage = [
  sprintf("%s        Thanks for installing Assegai%s 🙏\n", Color::YELLOW, Color::RESET),
  sprintf("%sPlease consider donating to our open collective\n", Color::DARK_WHITE, Color::RESET),
  sprintf("%s    to help us maintain this package.%s\n\n\n", Color::DARK_WHITE, Color::RESET),
];

foreach ($thankYouMessage as $line)
{
  $lineLength = strlen($line);
  $offset = (TermInfo::windowSize()->width() / 2) - ($lineLength / 2);
  for ($x = 0; $x < $offset; $x++)
  {
    echo ' ';
  }
  echo $line;
}

$donationLink = sprintf("🍷  %sDonate: https://opencollective.com/assegai\n\n", Color::RESET);
$lineLength = strlen($donationLink);
$offset = (TermInfo::windowSize()->width() / 2) - ($lineLength / 2);
for ($x = 0; $x < $offset; $x++)
{
  echo ' ';
}
echo $donationLink;