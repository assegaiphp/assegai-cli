#!/usr/bin/env php
<?php

use Assegai\CLI\LIB\Color;
use Assegai\CLI\LIB\Logging\Logger;
use Assegai\CLI\LIB\TextStyle;
use Assegai\CLI\LIB\Util\Console;
use Assegai\CLI\LIB\Util\TermInfo;

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
$cloneResult = system(command: "git clone --quiet $repositoryURL $projectName > output.txt", result_code: $resultCode);

// if (file_exists("$workingDirectory/output.txt"))
// {
//   $execOutput = file("$workingDirectory/output.txt");
//   unlink("$workingDirectory/output.txt");
// }

// exit;
// exit(var_export($execOutput, true));
// exit(str_replace("", '-', $cloneResult) . "\n");
$lastLine = array_shift($execOutput);
if (str_starts_with($lastLine, 'fatal: '))
{
  error_log(message: $cloneResult);
  Logger::error(message: "Failed to download the package files. Check the logs for more info.\n", exit: true);
}

printf(
  "%s%s\r%s✔%s Installation in progress... ☕\n\n",
  Console::cursor()::moveUp(return: true),
  Console::eraser()::entireLine(),
  Color::LIGHT_GREEN,
  Color::RESET);

printf("🚀  Successfully created project %s%s%s\n", Color::LIGHT_GREEN, $projectName, Color::RESET);
printf("👉  Get started with the following commands:\n\n");
printf("%s$ cd %s%s\n\n\n", Color::DARK_WHITE, $projectName, Color::RESET);

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