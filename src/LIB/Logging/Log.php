#!/usr/bin/php
<?php

namespace Assegai\CLI\LIB\Logging;

use DateTime;

class Log
{
  protected ?DateTime $timestamp = null;

  public function __construct(protected string $message)
  {
  }

  public function message(): string { return $this->message; }

  public function __toString(): string
  {
    $message = '';

    if (!is_null($this->timestamp))
    {
      $message = "[" . DateTime::ATOM . "] ";
    }

    return $message . $this->message();
  }
}