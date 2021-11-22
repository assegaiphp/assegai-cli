#!/usr/bin/env php
<?php

namespace Assegai\LIB\Util;

use Assegai\LIB\Color;

final class Console
{
  public static function color(): Color
  {
    return new Color;
  }

  public static function cursor(): ConsoleCursor
  {
    return new ConsoleCursor;
  }

  public static function eraser(): ConsoleEraser
  {
    return new ConsoleEraser;
  }

  public static function info(): TermInfo
  {
    return new TermInfo;
  }
}