#!/usr/bin/php
<?php

namespace Assegai\LIB\Menus;

class MenuOptions
{
  public function __construct(
    private bool $showDescriptions = false,
    private string $titleColor = 'yellow'
  ) { }

  public function showDescriptions(): bool { return $this->showDescriptions; }

  public function titleColor(): string { return $this->titleColor; }
}