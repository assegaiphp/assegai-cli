#!/usr/bin/php
<?php

namespace Assegai\CLI\LIB\Menus;

class MenuOptions
{
  public function __construct(
    private ?bool $showDescriptions = null,
    private bool $showIndexes = true,
    private string $titleColor = 'yellow'
  ) { }

  public function showDescriptions(): ?bool { return $this->showDescriptions; }

  public function showIndexes(): bool { return $this->showIndexes; }

  public function titleColor(): string { return $this->titleColor; }

  public function setShowDescriptions(bool $showDescriptions): void { $this->showDescriptions = $showDescriptions; }

  public function setShowIndexes(bool $showIndexes): void { $this->showIndexes = $showIndexes; }

  public function setTitleColor(string $titleColor): void { $this->titleColor = $titleColor; }
}