<?php

namespace AssegaiPHP\Modules\ResourceName;

use Assegai\Core\Attributes\Injectable;
use Assegai\Database\Attributes\InjectRepository;
use AssegaiPHP\Modules\ResourceName\DTO\CreateSingleResourceNameDto;
use AssegaiPHP\Modules\ResourceName\DTO\UpdateSingleResourceNameDto;

#[Injectable]
class ResourceNameService
{
  public function __construct(
    #[InjectRepository(SingleResourceNameEntity::class)]
    private Repository $SingleResourceNameLowerCaseRepository
  )
  { }

  public function create(CreateSingleResourceNameDto $createCaseDto)
  {
    return 'This action adds a new SingleResourceNameLowerCase';
  }

  public function findAll()
  {
    return `This action returns all ResourceNameLowerCase`;
  }

  public function findOne(int $id)
  {
    return `This action returns a #${id} SingleResourceNameLowerCase`;
  }

  public function update(int $id, UpdateSingleResourceNameDto $updateCaseDto)
  {
    return `This action updates a #${id} SingleResourceNameLowerCase`;
  }

  public function remove(int $id)
  {
    return `This action removes a #${id} SingleResourceNameLowerCase`;
  }
}