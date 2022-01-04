#!/usr/bin/php
<?php

namespace Assegai\CLI\LIB\Menus;

use Assegai\CLI\LIB\Color;
use JetBrains\PhpStorm\Pure;

class Menu
{
  private ?MenuItem $selected = null;

  /**
   * @param array<int, MenuItem> $items
   */
  public function __construct(
    private string $title,
    private array $items = [],
    private ?MenuOptions $options = null,
    private ?string $description = null,
    private ?string $helpTip = null
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

  public function description(): ?string { return $this->description; }

  public function setDescription(string $description): void { $this->description = $description; }

  public function helpTip(): ?MenuItem { return $this->helpTip; }

  public function setHelpTip(string $helpTip): void { $this->helpTip = $helpTip; }

  public function hasItem(string $index): bool
  {
    return key_exists(key: $index, array: $this->items);
  }

  public function getItemValue(string $valueOrAlias): string|bool
  {
    if ($this->hasItemWithValue(valueOrAlias: $valueOrAlias))
    {
      $value = null;

      foreach ($this->items as $item)
      {
        if ($item->value() === $valueOrAlias || $item->alias() === $valueOrAlias)
        {
          return $item->value();
        }
      }
    }

    return false;
  }

  public function hasItemWithValue(string $valueOrAlias): bool
  {
    $hasItem = false;
    
    foreach ($this->items as $item)
    {
      if ($item->value() === $valueOrAlias || $item->alias() === $valueOrAlias)
      {
        $hasItem = true;
      }
    }

    return $hasItem;
  }

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
    $description = $this->options()->showDescriptions() 
      ? $this->description() . "\n\n"
      : '';
    $titleColorCode = $this->getColorCode(color: $this->options()->titleColor());
    $itemsOutput = '';
    
    foreach ($this->items as $item)
    {
      $previousShowIndexes = $item->options()->showIndexes();

      if (!$this->options()->showIndexes())
      {
        $item->options()->setShowIndexes(false);
      }

      $itemsOutput .= $item->display(withDescriptions: $this->options->showDescriptions()) . "\n";

      $item->options()->setShowIndexes($previousShowIndexes);
    }

    return trim("${description}${titleColorCode}" . $this->title . "\e[0m\n$itemsOutput") . "\n";
  }

  public function prompt(string $message = 'Choose option', bool $useKeypad = false, bool $multiSelect = false): ?MenuItem
  {
    global $assegaiPath;

    if ($useKeypad)
    {
      $options = [];

      foreach ($this->items as $item)
      {
        $options[] = $item->value();
      }

      $selectedIndex = 0;
      $response = promptSelect(options: $options, message: $message, selectedIndex: $selectedIndex);

      $this->selected = $this->items[($selectedIndex + 1)];
    }
    else
    {
      $inputColorCode = $this->getColorCode(color: 'blue');
      printf("%s\n%s:$inputColorCode ", $this, $message);
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
    }

    echo Color::RESET;

    return $this->selected();
  }

  public function getHelp(): string
  {
    $help = "Available options:\n";

    if (!is_null($this->description()))
    {
      $help .= sprintf("%s\n\n", $this->description());
    }
    else
    {
      $help .= "\n";
    }

    foreach ($this->items as $item)
    {
      $help .= sprintf("  %-10s%s\n", $item->value(), $item->description());
    }

    if (!is_null($this->helpTip()))
    {
      $help .= sprintf("\n%s\n", $this->helpTip());
    }

    return $help . "\n";
  }

  public function help(): void
  {
    echo $this->getHelp();
  }

  public function describeItem(string $itemValueOrIndex): void
  {
    foreach ($this->items as $index => $item)
    {
      if (in_array($itemValueOrIndex, [$index, $item->value(), $item->alias()]))
      {
        $commandColor = Color::BLUE;
        $titleColor   = Color::YELLOW;
        $colorReset   = Color::RESET;

        printf(
          "${commandColor}%s${colorReset}\n  %s\n\n${titleColor}Full Description:${colorReset}\n%-2s%s\n",
          $item->value(),
          $item->description(),
          ' ',
          $item->fullDescription()
        );
        break;
      }
    }
  }

  private function getColorCode(string $color): string
  {
    return match ($color) {
      'black'   => Color::BLACK,
      'red'     => Color::RED,
      'green'   => Color::GREEN,
      'yellow'  => Color::YELLOW,
      'blue'    => Color::BLUE,
      'magenta' => Color::MAGENTA,
      'cyan'    => Color::CYAN,
      'white'   => Color::WHITE,
      default   => Color::RESET
    };
  }
}