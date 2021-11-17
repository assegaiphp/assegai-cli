#!/usr/bin/env php
<?php

namespace Assegai\Commands\Generate;

use Assegai\LIB\Generation\SchematicBuilder;

$schematicBuilder = new SchematicBuilder;

list($name) = match (count($args)) {
  1 => [null],
  default => array_slice($args, 1)
};

$schematicBuilder->buildController(name: $name);