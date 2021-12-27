<?php
# Load configuration
require_once 'autoload-config.php';

# Autoload Workspace Classes
spl_autoload_register(function ($class) {
  $namespaces = explode('\\', $class);
  if ($namespaces[0] == 'AssegaiPHP')
  {
    $namespaces[0] = 'src';
  }
  $classpath = implode('/', $namespaces) . '.php';

  if (!file_exists("app/$classpath"))
  {
    error_log("Unknown file: $classpath", 0);
    http_response_code(500);
    exit(1);
  }

  require_once $classpath;
});
