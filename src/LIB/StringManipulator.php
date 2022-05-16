#!/usr/bin/env php
<?php

namespace Assegai\CLI\LIB;

use plejus\PhpPluralize\Inflector;


class StringManipulator
{
  public static function camelCaseToSnakeCase(string $input): string
  {
    $strlen = strlen($input);
    $output = '';
    $word = '';
    $tokens = [];

    for ($x = 0; $x < $strlen; $x++) {
      $ch = substr($input, $x, 1);

      if (ctype_upper($ch)) {
        $tokens[] = $word;
        $word = '';
      }

      $word .= $ch;
    }

    $tokens[] = $word;
    $output = implode('_', $tokens);

    return strtolower($output);
  }

  public static function snakeCaseToCamelCase(string $input): string
  {
    $replacement = str_replace('_', ' ', $input);
    $buffer = ucwords($replacement);
    $output = str_replace(' ', '', $buffer);

    return lcfirst($output);
  }

  public static function pascalCaseToSnakeCase(string $input): string
  {
    $output = self::pascalCaseToCamelCase(input: $input);

    return self::camelCaseToSnakeCase(input: $output);
  }

  public static function snakeCaseToPascalCase(string $input): string
  {
    $tokens = explode('_', $input);

    $output = array_map(function ($token) {
      return strtoupper(substr($token, 0, 1)) . strtolower(substr($token, 1));
    }, $tokens);

    return implode($output);
  }

  public static function pascalCaseToCamelCase(string $input): string
  {
    return lcfirst($input);
  }

  public static function camelCaseToPascalCase(string $input): string
  {
    return ucfirst($input);
  }

  public static function getPluralForm(string $word): string
  {
    $inflector = new Inflector();
    return $inflector->plural($word);
  }

  public static function getSingularForm(string $word): string
  {
    $inflector = new Inflector();
    return $inflector->singular($word);
  }
}