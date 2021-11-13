#!/usr/bin/php
<?php

namespace Assegai\LIB\Menus;

use JetBrains\PhpStorm\Pure;

class Menu
{
  private ?MenuItem $selected = null;

  public function __construct(
    private string $title,
    private array $items = [],
    private ?MenuOptions $options = null
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
  
  public function title(): string { return $this->title; }

  public function setTitle(string $title): void { $this->title = $title; }
  
  public function options(): MenuOptions { return $this->options; }

  public function setOptions(MenuOptions $options): void { $this->otpions = $options; }

  public function selected(): ?MenuItem { return $this->selected; }

  public function add(MenuItem $item): void
  {
    if (!key_exists($item->index(), $this->items))
    {
      $count = count($this->items) + 1;
      if (is_null($item->index()))
      {
        $item->setIndex(index: $count);
      }
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

  public function clear(): void
  {
    $this->items = [];
  }

  #[Pure]
  public function __toString(): string
  {
    $titleColorCode = $this->getColorCode(color: $this->options()->titleColor());
    $itemsOutput = '';
    
    foreach ($this->items as $item)
    {
      $itemsOutput .= $item->display(withDescriptions: $this->options->showDescriptions()) . "\n";
    }

    return "\n$titleColorCode" . $this->title . "\e[0m\n\n$itemsOutput\n";
  }

  public function prompt(string $message = 'Choose option'): ?MenuItem
  {
    $inputColorCode = $this->getColorCode(color: 'blue');
    echo $this . "$message: $inputColorCode";
    $attemptsLeft = 4;
    $isValidChoice = false;
    $colorCode = $this->getColorCode(color: 'magenta');

    do
    {
      $choice = trim(fgets(STDIN));
      --$attemptsLeft;
      $isValidChoice = isset($this->items[$choice]);

      if ($isValidChoice)
      {
        $this->selected = $this->items[$choice];
      }
      else
      {
        if ($attemptsLeft <= 0)
        {
          $colorCode = $this->getColorCode(color: 'red');
          exit("\n${colorCode}Program terminating...\e[0m\n");
        }
        echo "\n${colorCode}Invalid choice. Try again!\n$attemptsLeft attempts left...\e[0m\n\n$message: $inputColorCode";
      }
    }
    while(!$isValidChoice);
    echo "\e[0m";

    return $this->selected();
  }

  private function getColorCode(string $color): string
  {
    return match ($color) {
      'black'   => "\e[0;30m",
      'red'     => "\e[0;31m",
      'green'   => "\e[0;32m",
      'yellow'  => "\e[0;33m",
      'magenta' => "\e[0;35m",
      'cyan'    => "\e[0;36m",
      'white'   => "\e[0;37m",
      default   => "\e[0;34m"
    };
  }
}