#!/usr/bin/php
<?php

namespace Assegai\CLI\Menus;

use JetBrains\PhpStorm\Pure;

class Menu
{
  public function __construct(
    private string $title,
    private array $items = [],
    private ?MenuOptions $options
  )
  {
    if (is_null($this->options))
    {
      $this->options = new MenuOptions();
    }

    if (!empty($items))
    {
      $buffer = $this->items;
      $this->items = [];

      $this->addRange(items: $buffer);
    }
  }
  
  public function options(): MenuOptions { return $this->options; }

  public function setOptions(MenuOptions $options): void { $this->otpions = $options; }

  public function add(MenuItem $item): void
  {
    if (!key_exists($item->index(), $this->items))
    {
      $count = count($this->items) + 1;
      $item->setIndex(index: $count);
      $this->items[$item->index()] = $item;
    }
    else
    {
      $errorMessage = 'WARNING: Duplicate MenuItem(' . $item->value() . ')';
      error_log(message: $errorMessage);
    }
  }

  public function addRange(array $items): void
  {
    foreach ($items as $item)
    {
      if ($item instanceof MenuItem)
      {
        $this->add(item: $item);
      }
    }
  }

  public function remove(MenuItem|int $item): void
  {
    $index = ($item instanceof MenuItem) ? $item->index() : $item;

    if (isset($this->items[$index]))
    {
      unset($this->items[$index]);
    }
  }

  public function removeRange(array $items): void
  {
    foreach ($items as $item)
    {
      if ($item instanceof MenuItem || is_integer($item))
      {
        $this->remove(item: $item);
      }
    }
  }

  #[Pure]
  public function __toString(): string
  {
    $titleColorCode = $this->getColorCode(color: $this->options->titleColor());
    $itemsOutput = '';
    
    foreach ($this->items as $item)
    {
      $itemsOutput .= $item->display(withDescriptions: $this->options->showDescriptions()) . "\n";
    }

    return "$titleColorCode" . $this->title . "\e[0m\n$itemsOutput\n";
  }

  private function getColorCode(string $color): string
  {
    return match ($color) {
      'black'   => "\e[1;30m",
      'red'     => "\e[1;31m",
      'green'   => "\e[1;32m",
      'yellow'  => "\e[1;33m",
      'magenta' => "\e[1;35m",
      'cyan'    => "\e[1;36m",
      'white'   => "\e[1;37m",
      default   => "\e[1;34m"
    };
  }
}