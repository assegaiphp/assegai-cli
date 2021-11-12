#!/usr/bin/php
<?php

namespace Assegai\CLI\Menus;

class MenuOptions
{
  public function __construct(
    private bool $showDiscriptions = false,
    private string $titleColor = 'yellow'
  ) { }

  public function showDescriptions(): bool { return $this->showDescriptions; }

  public function titleColor(): bool { return $this->titleColor; }
}