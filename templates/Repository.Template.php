<?php

namespace LifeRaft\Modules\RepositoryName;

use LifeRaft\Core\Attributes\Injectable;
use LifeRaft\Database\Attributes\Repository;
use LifeRaft\Database\BaseRepository;

#[Repository(
  entity: '',
  tableName: 'TableName'
)]
#[Injectable]
class RepositoryTemplate extends BaseRepository
{
}

?>