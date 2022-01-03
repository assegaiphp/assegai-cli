<?php

namespace AssegaiPHP\GuardNamespace;

use Assegai\Core\Attributes\Injectable;
use Assegai\Core\ExecutionContext;
use Assegai\Core\Interfaces\ICanActivate;

#[Injectable]
class GuardName implements ICanActivate
{
  public function canActivate(ExecutionContext $context): bool
  {
    return true;
  }

  public function canDeactivate(ExecutionContext $context): bool
  {
    return true;
  }
}