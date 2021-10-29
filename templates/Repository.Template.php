<?php

namespace LifeRaft\Modules\RepositoryName;

use LifeRaft\Core\Attributes\Injectable;
use LifeRaft\Database\Attributes\Repository;
use LifeRaft\Database\Interfaces\IEntity;
use LifeRaft\Database\Interfaces\IRepository;

#[Repository(
  entity: TestEntity::class,
  tableName: 'TableName'
)]
#[Injectable]
class RepositoryTemplate implements IRepository
{
}

?>