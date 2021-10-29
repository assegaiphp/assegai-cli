<?php

namespace LifeRaft\Modules\ModuleName;

use LifeRaft\Core\BaseController;
use LifeRaft\Core\Responses\Response;
use LifeRaft\Core\Attributes\Get;

class ControllerTemplate extends BaseController
{
  protected array $forbidden_methods = [];

  #[Get]
  public function findAll(): Response
  {
    return new Response( data: ['This action returns all entities'], dataOnly: true );
  }

  #[Get(path: '/:id')]
  public function find(int $id): Response
  {
    return new Response( data: ['This action returns the entity with id: ' . $id], dataOnly: true );
  }
}

?>