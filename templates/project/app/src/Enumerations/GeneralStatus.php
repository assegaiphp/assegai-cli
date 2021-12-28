<?php

namespace AssegaiPHP\Enumerations;

enum GeneralStatus: string
{
  case ACTIVE   = 'active';
  case INACTIVE = 'inactive';
  case HOLD     = 'hold';
  case ARCHIVED = 'archived';

  public function color(ColorMode $mode = ColorMode::HEX): string
  {
    return match($this) {
      GeneralStatus::ACTIVE => match($mode) {
        ColorMode::RGB => '1,2,3',
        ColorMode::RGB => '1,2,3,0.4',
        ColorMode::HSL => '',
        default => '#AA1923'
      },
      default => match($mode) {
        default => '#AA1923'
      }
    };
  }

  public static function list(bool $raw = false): array
  {
    return $raw
      ? [
        GeneralStatus::ACTIVE,
        GeneralStatus::INACTIVE,
        GeneralStatus::HOLD,
        GeneralStatus::ARCHIVED
      ]
      : [
        GeneralStatus::ACTIVE->value,
        GeneralStatus::INACTIVE->value,
        GeneralStatus::HOLD->value,
        GeneralStatus::ARCHIVED->value
      ];
  }
}