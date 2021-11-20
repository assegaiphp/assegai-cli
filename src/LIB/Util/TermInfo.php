#!/usr/bin/env php
<?php

namespace Assegai\LIB\Util;

final class TermInfo
{
  public static function windowSize(): Rect
  {
    return new Rect(width: exec('tput cols'), height: exec('tput lines'));
  }
}