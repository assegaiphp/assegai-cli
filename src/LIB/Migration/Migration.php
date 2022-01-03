#!/usr/bin/php
<?php

namespace Assegai\CLI\LIB\Migration;

use JetBrains\PhpStorm\Pure;

final class Migration
{
  public function __construct(
    private string $name,
    private ?string $ranOn = null,
  ) { }

  public function name(): string
  {
    return $this->name;
  }

  public function value(): int
  {
    return intval(substr($this->name, 0, 14));
  }

  public function ranOn(): ?string
  {
    return $this->ranOn;
  }

  public function setRanOn(string $ranOn): void
  {
    $this->ranOn = $ranOn;
  }

  #[Pure]
  public function __toString(): string
  {
    $checkMark = is_null($this->ranOn) ? '[ ]' : '[x]';

    return $checkMark . ' ' . $this->name;
  }
}