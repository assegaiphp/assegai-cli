<?php

if ($argc > 1)
{
  list($moduleName, $targetArray, $newEntry) = array_slice($argv, 1);

  $moduleFilePath = "app/src/Modules/$moduleName/${moduleName}Module.php";
  if (file_exists($moduleFilePath))
  {
    $lines = file($moduleFilePath);

    $startIndex = null;
    $endIndex = null;
    $startsAndEndsOnSameLine = false;
    $targetArray = strtolower($targetArray);
    $output = [];

    foreach ($lines as $index => $line)
    {
      if (str_contains($line, "$targetArray:"))
      {
        $startIndex = $index;
        if (preg_match("/\],\n\r*$/", $line))
        {
          $lines[$index] = str_contains($line, "$targetArray: []") 
          ? str_replace("$targetArray: []", "$targetArray: [${newEntry}::class]", $line)
          : str_replace("],", ", ${newEntry}::class],", $line);
        }
      }
      
      if(is_numeric($startIndex) && is_null($endIndex))
      {
        if (str_ends_with($line, "],\n"))
        {
          $endIndex = $index;
        }
      }
    }

    $startsAndEndsOnSameLine = $startIndex === $endIndex;
    $output = "";

    if (is_numeric($endIndex) && !$startsAndEndsOnSameLine)
    {
      if (!str_ends_with($lines[$endIndex - 1], ",\n"))
      {
        $lines[$endIndex - 1] = str_replace("\n", ",\n", $lines[$endIndex - 1]);
      }
      $lines = array_merge(
        array_slice($lines, 0, $endIndex),
        ["    ${newEntry}::class,"],
        array_slice($lines, $endIndex)
      );
    }


    foreach ($lines as $line)
    {
      $output .= trim($line, "\n\r") . "\n";
    }

    $bytes = file_put_contents($moduleFilePath, trim($output));

    echo "\e[1;34mUPDATE\e[0m ${moduleFilePath} ($bytes bytes)\n";
  }
}

?>