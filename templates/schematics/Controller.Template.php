<?php

namespace AssegaiPHP\Modules\ModuleName;

use Assegai\Core\Attributes\Controller;
use Assegai\Core\Attributes\Delete;
use Assegai\Core\BaseController;
use Assegai\Core\Responses\Response;
use Assegai\Core\Attributes\Get;
use Assegai\Core\Attributes\Patch;
use Assegai\Core\Attributes\Post;
use Assegai\Core\Attributes\Put;
use Assegai\Database\Interfaces\IEntity;
use stdClass;

#[Controller(path: 'PathName')]
class ModuleNameController extends BaseController
{
  #[Get]
  public function findAll(): Response
  {
    return new Response( data: ['This action returns all PathName'], dataOnly: true );
  }

  #[Get(path: '/:id')]
  public function find(int $id): Response
  {
    return new Response( data: ['This action returns the PathName entity with id: ' . $id], dataOnly: true );
  }

  #[Post]
  public function create(stdClass|array $body): Response
  {
    return new Response( data: ['This action creates a new PathName entity'], dataOnly: true );
  }

  #[Put(path: '/:id')]
  public function update(int $id, stdClass|IEntity $body): Response
  {
    return new Response( data: ['This action fully updates the PathName entity with id: ' . $id], dataOnly: true );
  }

  #[Patch(path: '/:id', action: Patch::UPDATE_ACTION)]
  public function partialUpdate(int $id, stdClass $body): Response
  {
    return new Response(data: ['This action partially updates the PathName entity with id: ' . $id], dataOnly: true);
  }

  #[Patch(path: '/:id', action: Patch::RESTORE_ACTION)]
  public function restore(int $id): Response
  {
    return new Response(data: ['This action restores the PathName entity with id: ' . $id], dataOnly: true);
  }

  #[Patch(path: '/:id', action: Patch::DELETE_ACTION)]
  public function softRemove(int $id): Response
  {
    return new Response(data: ['This action soft-removes the PathName entity with id: ' . $id], dataOnly: true);
  }

  #[Delete]
  public function removeAll(array $ids): Response
  {
    $idsString = implode(', ', $ids);
    return new Response(data: "This action removes all PathName entities with ids $idsString", dataOnly: true);
  }

  #[Delete(path: '/:id')]
  public function remove(int $id): Response
  {
    return new Response(data: ['This action removes the PathName entity with id: ' . $id], dataOnly: true);
  }
}
