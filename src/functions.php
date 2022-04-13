#!/usr/bin/env php
<?php

use Assegai\CLI\LIB\Color;
use Assegai\CLI\LIB\Logging\Logger;
use Assegai\CLI\LIB\Util\Console;
use Assegai\CLI\LIB\Util\ConsoleCursor;
use Assegai\CLI\LIB\Util\TermInfo;
use Assegai\CLI\LIB\WorkspaceManager;
use Iber\Phkey\Environment\Detector;
use Iber\Phkey\Events\KeyPressEvent;

/**
 * Checks the `workding directory` for an `assegai.json` file. If no 
 * project file is found it will log an error.
 * 
 * @param null|string $commandName The name of the command that requires 
 * an assegai workspace to run.
 */
function checkWorkspace(?string $commandName = null): void
{
  if (is_null($commandName))
  {
    $commandName = substr(basename(__FILE__), 0, -4);
  }

  if (!WorkspaceManager::isAssegaiWorkspace())
  {
    Logger::error("The $commandName command requires to be run in an Assegai workspace, but a project definition could not be found.", exit: true);
  }
}

function prompt(string $message = 'Enter choice', ?string $defaultValue = null, ?int $attempts = null): string
{
  $defaultHint = '';
  $line = '';
  if (!empty($defaultValue))
  {
    $defaultHint = Color::DARK_WHITE . "($defaultValue) " . Color::RESET;
  }

  $isValid = false;
  $attemptsLeft = $attempts;

  do
  {
    printf("%s?%s %s: %s%s", Color::GREEN, Color::RESET, $message, $defaultHint, Color::LIGHT_BLUE);
    $line = trim(fgets(STDIN));
    echo Color::RESET;

    if (is_null($attemptsLeft))
    {
      $isValid = true;
    }
    else
    {
      if(empty($line) && !empty($defaultValue))
      {
        $line = $defaultValue;
      }

      --$attemptsLeft;
      if (!empty($line))
      {
        $isValid = true;
      }
      else if ($attemptsLeft === 0)
      {
        exit(1);
      }
      else
      {
        printf("%sInvalid input: %d attempts left%s\n", Color::MAGENTA, $attemptsLeft, Color::RESET);
      }
    }
  }
  while(!$isValid);

  if (empty($line) && !is_null($defaultValue))
  {
    $line = $defaultValue;
  }

  return $line;
}

function promptPassword(string $message = 'Password', ?int $attempts = null): string
{
  # Turn echo off
  `/bin/stty -echo`;

  $line = prompt(message: $message, attempts: $attempts);

  # Turn echo no
  `/bin/stty echo`;
  echo "\n";

  return $line;
}

function confirm(string $message, bool $defaultYes = true): bool
{
  $suffix = $defaultYes ? 'Y/n' : 'y/N';
  $response = $defaultYes ? true : false;
  $defaultHint = Color::DARK_WHITE . "($suffix) " . Color::RESET;

  $line = '';

  printf("%s?%s %s: %s%s", Color::GREEN, Color::RESET, $message, $defaultHint, Color::LIGHT_BLUE);
  $line = trim(fgets(STDIN));

  if (!empty($line))
  {
    $response = match(strtolower($line)) {
      'yes',
      'y',
      'yeah',
      'yep',
      'correct',
      'true',
      'affirmative' => true,
      default       => false
    };
  }
  if ($response === $defaultYes)
  {
    Console::cursor()::moveUpBy(numberOfLines: 1);
    Console::eraser()::entireLine();
    $suffix = $defaultYes ? 'Y' : 'N';
    $defaultHint = Color::LIGHT_BLUE . "$suffix " . Color::RESET;
    printf("\r%s?%s %s: %s%s\n", Color::GREEN, Color::RESET, $message, $defaultHint, Color::LIGHT_BLUE);
  }
  echo Color::RESET;

  return $response;
}

function promptSelect(array $options, ?string $message = null, int &$selectedIndex = 0, bool $multiSelect = false): string|array
{
  $GLOBALS['selectedOption'] = null;
  $GLOBALS['promptOptions'] = $options;
  $GLOBALS['totalOptions'] = count($options);
  $GLOBALS['selectedIndex'] = $selectedIndex;
  $GLOBALS['multiSelect'] = $multiSelect;
  
  $checkedOptions = [];
  
  if ($multiSelect)
  {
    foreach ($options as $index => $option)
    {
      $checkedOptions[$index] = false; 
    }
  }

  $GLOBALS['checkedOptions'] = $checkedOptions;

  printf("\r%s?%s %s: %s\n", Color::GREEN, Color::RESET, $message, Color::RESET);

  printOptions(options: $options, selectedIndex: $selectedIndex);

  ConsoleCursor::setVisibility(isVisible: false);
  $detector = new Detector();
  $listener = $detector->getListenerInstance();

  $eventDispatcher = $listener->getEventDispatcher();

  $eventDispatcher->addListener('key:press', function (KeyPressEvent $event) {
    global $selectedIndex, $totalOptions, $promptOptions, $multiSelect, $checkedOptions;

    switch ($event->getKey())
    {
      case 'up':
        --$selectedIndex;
        break;

      case 'down':
        ++$selectedIndex;
        break;
      
      case 'space':
        if ($multiSelect && $checkedOptions)
        {
          if (isset($checkedOptions[$selectedIndex]))
          {
            $checkedOptions[$selectedIndex] = !$checkedOptions[$selectedIndex];
          }
        }
        break;
    }

    $selectedIndex = wrap($selectedIndex, 0, $totalOptions);
    printOptions(options: $promptOptions, selectedIndex: $selectedIndex);
  });

  $eventDispatcher->addListener('key:enter', function (KeyPressEvent $event) use ($eventDispatcher) {
    global $selectedOption, $promptOptions, $selectedIndex;
    $selectedOption = $promptOptions[$selectedIndex];

    clearOptions(options: $promptOptions);
    ConsoleCursor::setVisibility(isVisible: true);

    $eventDispatcher->dispatch('key:stop:listening');
  });

  $listener->start();

  $selectedIndex = $GLOBALS['selectedIndex'];
  $selectedOption = $multiSelect
    ? ''
    : $options[$selectedIndex];

  ConsoleCursor::moveUp();
  printf("\r%s?%s %s: %s%s%s\n", Color::GREEN, Color::RESET, $message, Color::LIGHT_BLUE, $selectedOption, Color::RESET);

  $selectedOptions = [];
  if ($multiSelect)
  {
    $checkedOptions = $GLOBALS['checkedOptions'];

    foreach ($checkedOptions as $index => $value)
    {
      if ($value)
      {
        $selectedOptions[] = $options[$index];
      }
    }
  }

  return match (true) {
    $multiSelect => $selectedOptions,
    default => $selectedOption
  };
}

function printOptions(array $options, int $selectedIndex = 0): void
{
  global $multiSelect, $checkedOptions;
  $totalOptions = count($options);

  if (is_null($selectedIndex))
  {
    $selectedIndex = 0;
  }

  foreach ($options as $index => $option)
  {
    Console::eraser()->entireLine();
    $prefix = ' ';

    if ($multiSelect)
    {
      $prefix = $checkedOptions[$index]
        ? sprintf("%s%s%s", Color::LIGHT_GREEN, '◉', Color::RESET)
        : sprintf("%s%s", Color::RESET, '◯');
    }

    $color = ($index === $selectedIndex)
      ? sprintf("%s❯%s%s ", Color::LIGHT_BLUE, $prefix, Color::LIGHT_BLUE)
      : sprintf("%s %s ", Color::RESET, $prefix);
    printf("%s%s%s\n", $color, $option, Color::RESET);
  }

  Console::cursor()->moveUpBy(numberOfLines: $totalOptions);
}

function clearOptions(array $options)
{
  $totalOptions = count($options);
  $terminalWidth = TermInfo::windowSize()->width();

  Console::cursor()->moveDownBy(numberOfLines: $totalOptions);
  Console::eraser()->entireLine();

  for ($x = 0; $x < $totalOptions; $x++)
  {
    Console::cursor()->moveUp();
    Console::eraser()->entireLine();
  }
}

function printBlankSpaces(int $numberOfSpaces = 1): void
{
  for ($x = 0; $x < $numberOfSpaces; $x++)
  {
    echo ' ';
  }
}

function bytesFormat(?int $bytes): string
{
  if (is_null($bytes))
  {
    $bytes = 0;
  }
  return match (true) {
    $bytes < 1024 => "$bytes Bytes",
    $bytes < 1048576 => number_format($bytes / 1024, 2) . " KB",
    $bytes < 1073741824 => number_format($bytes / 1048576, 2) . " MB",
    $bytes < 1099511627776 => number_format($bytes / 1048576, 2) . " GB",
    default => "$bytes Bytes"
  };
}

function getHeader(): string
{
  global $assegaiPath;
  $content = file_get_contents(sprintf("%s/src/header.txt", $assegaiPath));
  $output = Color::RED;
  $output .= $content;
  $output .= Color::RESET;
  return $output;
}

function printHeader(): void
{
  echo getHeader() . "\n";
}

function getVersion(): string
{
  global $workingDirectory, $assegaiPath;
  $version = exec("cd $assegaiPath && composer global show assegaiphp/assegai-cli | grep versions && cd $workingDirectory") . "\n";
  return $version;
}

function getFrameworkVersion(): string
{
  $version = exec("composer show assegaiphp/assegai | grep versions") . "\n";
  return $version;
}

function printVersion(): void
{
  echo getVersion();
}

function jsonPrettify(string $json): string
{
  $output = json_encode(json_decode(json: $json), JSON_PRETTY_PRINT);
  $output = preg_replace('/(".*"):\s*(".*")*,*/', sprintf("%s%s%s: %s%s%s", Color::LIGHT_BLUE, "$1", Color::RESET, Color::YELLOW, "$2", Color::RESET), $output);
  
  if (!str_ends_with($output, "\n"))
  {
    $output .= "\n";
  }

  return $output;
}

function isJson(string $json): bool
{
  json_decode($json);
  return json_last_error() === JSON_ERROR_NONE;
}

function clamp(int|float $value, int|float $min, int|float $max): int|float
{
  if ($value < $min)
  {
    return $min;
  }

  if ($value > $max) {
    return $max;
  }

  return $value;
}

function wrap(int $value, int $min, int $max): int
{
  if ($value < $min)
  {
    return $max - 1;
  }

  if ($value >= $max)
  {
    return $min;
  }

  return $value;
}

function getLoadingBar(int $percentage = 0)
{
  $percentage = clamp($percentage, 0, 100);
  $bar = '';
  $space = '';
  
  for ($x = 0; $x < $percentage; $x++)
 	{
    $bar .= '█';
  }
  
  for ($x = $percentage; $x < 100; $x++)
  {
    $space .= '░';
  }
       
  return sprintf("%-6s[%s%s]<br>", "${percentage}%", $bar, $space);
}

function printLoadingBad(int $percentage = 0)
{
  return getLoadingBar(percentage: $percentage);
}

function pascalToSnake(string $term): string
{
  $length = strlen($term);
  $token = '';

  for ($x = 0; $x < $length; $x++)
  {
    $ch = substr($term, $x, 1);

    if (ctype_upper($ch))
    {
      $token .= $x === 0 ? $ch : "_$ch";
    }
    else
    {
      $token .= strtolower($ch);
    }
  }

  return strtolower($token);
}

function snakeToPascal(string $word): string
{
  $tokens = explode('_', $word);
  $result = '';
  
  foreach ($tokens as $token)
  {
    $result .= strtoupper(substr($token, 0, 1));
    $result .= substr($token, 1);
  }
  
  return $result;
}

function snakeToCamel(string $word): string
{
  $buffer = snakeToPascal(word: $word);
  $token 	= strtolower(substr($buffer, 0, 1));
  $token .= substr($buffer, 1);
  
  return $token;
}

/**
 * @param string $shortOptions Each character in this string will be used as option characters and matched against options passed to the script starting with a single hyphen (-). For example, an option string "x" recognizes an option -x. Only a-z, A-Z and 0-9 are allowed.
 * @param array<int, string> $longOptions An array of options. Each element in this array will be used as option strings and matched against options passed to the script starting with two hyphens (--). For example, an longopts element "opt" recognizes an option --opt.
 * @param int &$restIndex If the restIndex parameter is present, then the index where argument parsing stopped will be written to this variable.
 * @param int $offset Spacifies which word from the args array to start parsing from.
 * 
 */
function getoptions(string $shortOptions, array $longOptions, int &$restIndex, int $offset = 0): array
{
  global $argv;
  $options = [];

  # Tokenize short options
  $tokens = str_split($shortOptions);
  $optionsTokenList = [];

  foreach ($tokens as $index => $token)
  {
    if ($token === ctype_alpha($token))
    {
      $optionsTokenList[$token] = match(true) {
        str_starts_with(substr($shortOptions, $index), "${token}::") => false,
        str_starts_with(substr($shortOptions, $index), "${token}:") => true,
        default => false,
      };
    }
  }

  foreach ($longOptions as $index => $token)
  {
    if (str_ends_with($token, '::'))
    {
      $optionsTokenList[substr($token, 0, -2)] = false;
    }
    else if (str_ends_with($token, ':'))
    {
      $optionsTokenList[substr($token, 0, -1)] = true;
    }
    else
    {
      $optionsTokenList[$token] = false;
    }
  }

  $arguments = array_slice(array: $argv, offset: $offset + 1);

  $skipNext = false;
  $argValueIndex = null;
  $matches = [];
  $value = null;

  foreach ($arguments as $index => $arg)
  {
    $next = next($arguments);

    if ($skipNext)
    {
      $skipNext = false;
      continue;
    }

    if (preg_match('/^[-]+([\w]+)(=\"*([\s\w.=\d-]+)\"*)*/', $arg, $matches) !== false)
    {
      if (empty($matches))
      {
        $restIndex = $index;
        break;
      }
      $key = trim($matches[1]);
      $value = $matches[3] ?? null;

      if (!isset($optionsTokenList[$key]))
      {
        continue;
      }

      $options[$key] = false;

      # If key requires value 
      if (
        $optionsTokenList[$key] &&
        is_null($value) &&
        str_starts_with($next, '-')
      )
      {
        # and no value is found, log error
        Logger::error(message: sprintf("Option '%s' requires a value but none found at index '%d'", $key, $index), exit: true);
      }

      if (!is_null($value))
      {
        $options[$key] = match(true) {
          is_numeric($value) => is_string($value) && str_contains($value, '.') ? floatval($value) : intval($value),
          default => $value
        };
      }
      else if ($next !== false)
      {
        if (!str_starts_with($next, '-'))
        {
          $options[$key] = match (true) {
            is_numeric($next) => is_string($next) && str_contains($next, '.') ? floatval($next) : intval($next),
            default => $next
          };
          $skipNext = true;
        }
      }
    }
    else
    {
      $restIndex = $index;
      break;
    }
  }

  return $options;
}

function updateArrayFile(string $filename, array $replacement): int|false
{
  $bytes = 0;

  if (!file_exists($filename))
  {
    return false;
  }
  
  $fileContent = file_get_contents(filename: $filename);

  $fileArray = include($filename);

  if (!is_array($fileArray))
  {
    return false;
  }
  
  $fileArray = array_replace($fileArray, $replacement);
  $fileArrayFormatted = str_replace('array (', '[', var_export($fileArray, true));
  $fileArrayFormatted = str_replace(')', ']', $fileArrayFormatted);
  $fileArrayFormatted = preg_replace('/=>[\s]*\n[\s]*\[/', '=> [', $fileArrayFormatted);
  $fileContentReturnPos = strpos($fileContent, 'return');
  $fileContentPrefix = substr($fileContent, 0, $fileContentReturnPos);
  $data = $fileContentPrefix . "return " . $fileArrayFormatted . ";\n";

  $bytes = file_put_contents(filename: $filename, data: $data);

  return is_bool($bytes) ? false : $bytes;
}

/**
 * Returns a sanitized string representation of the given database name.
 * 
 * @param string $databaseName The name of the database.
 * 
 * @return string Returns a sanitized string representation of the given database name.
 */
function getSanitizedDBName(string $databaseName): string
{
  return preg_replace('/[!@#$%^&*()-=\+ ]+/', '_', filter_var($databaseName, FILTER_SANITIZE_ENCODED));
}