<?php

namespace Assegai\CLI\Exceptions;

class NotImplementedException extends BaseAssegaiException
{
  public function __construct(string $message = 'Feature not developed yet.')
  {
    parent::__construct(message: $message, code: BaseAssegaiException::NOT_IMPELMENTED);
  }
}