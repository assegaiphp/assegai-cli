<?php

namespace Assegai\Modules\RepositoryName;

use Assegai\Core\Attributes\Injectable;
use Assegai\Database\Attributes\Repository;
use Assegai\Database\BaseRepository;

#[Repository(
  entity: '',
  tableName: 'TableName'
)]
#[Injectable]
class RepositoryTemplate extends BaseRepository
{
}

?>