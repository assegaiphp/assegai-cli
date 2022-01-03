#!/usr/bin/env php
<?php

namespace Assegai\CLI\LIB\Util;

final class Rect
{
  public function __construct(
    private int|float $width = 0,
    private int|float $height = 0,
  ) { }

  public function width(): int|float { return $this->width; }

  public function height(): int|float { return $this->height; }
}