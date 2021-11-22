#!/usr/bin/env php
<?php

namespace Assegai\CLI\Commands\Generate;

use Assegai\CLI\LIB\Generation\SchematicBuilder;

list($name) = match (count($args)) {
  1 => [null],
  default => array_slice($args, 1)
};

$schematicBuilder = new SchematicBuilder;
$schematicBuilder->buildService(name: $name);
