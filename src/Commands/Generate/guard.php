#!/usr/bin/env php
<?php

echo sprintf("%s%25s", "Loading", "0%");

for ($x = 1; $x < 101; $x++)
{
  echo sprintf("\r%s%25s", "Loading", "$x%");
  usleep(10000000 / 500);
  if ($x === 100)
  {
    echo "\n";
  }
}