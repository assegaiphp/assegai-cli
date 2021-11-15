<?php

namespace Assegai\Modules\ServiceName;

use Assegai\Core\Attributes\Injectable;
use Assegai\Core\Result;
use Assegai\Core\BaseService;

#[Injectable]
class ServiceTemplate extends BaseService
{
  public function findAll(): Result
  {
    return new Result();
  }

  public function find(int $id): Result
  {
    return new Result();
  }

  public function create(mixed $entity): Result
  {
    return new Result();
  }

  public function update(): Result
  {
    return new Result();
  }

  public function patch(): Result
  {
    return new Result();
  }

  public function delete(): Result
  {
    return new Result();
  }
}
