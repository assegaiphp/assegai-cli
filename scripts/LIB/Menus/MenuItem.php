#!/usr/bin/php
<?php

namespace Assegai\LIB\Menus;

class MenuItem
{
  public function __construct(
    private string $value,
    private string $description = '',
    private string $index = '',
    // TODO: change Enum in PHP 8.1
    private string $indexColor = 'blue'
  ) { }

  public function value(): string { return $this->value; }

  public function description(): string { return $this->description; }

  public function index(): string { return $this->index; }

  public function setIndex(string $index): void { $this->index = $index; }

  public function __toString(): string
  {
    $color = strtolower($this->indexColor);
    $indexColorCode = match($color) {
      'black'   => "\e[1;30m",
      'red'     => "\e[1;31m",
      'green'   => "\e[1;32m",
      'yellow'  => "\e[1;33m",
      'magenta' => "\e[1;35m",
      'cyan'    => "\e[1;36m",
      'white'   => "\e[1;37m",
      default   => "\e[1;34m"
    };

    return "$indexColorCode" . $this->index . "\e[0m) " . $this->value;
  }

  public function display(bool $withDescriptions = false): string
  {
    $output = strval($this);

    if ($withDescriptions)
    {
      $output .= " " . $this->description();
    }

    return $output;
  }

  public function print_display(bool $withDescriptions = false): void
  {
    echo $this->display(withDescriptions: $withDescriptions);
  }
}
