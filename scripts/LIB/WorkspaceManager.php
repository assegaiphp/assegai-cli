#!/usr/bin/php
<?php

namespace Assegai\LIB;

final class WorkspaceManager
{
  public static function isAssegaiWorkspace(): bool
  {
    global $workingDirectory;
    $configFile = sprintf("%s/assegai.json", $workingDirectory);
    return file_exists($configFile);
  }
}