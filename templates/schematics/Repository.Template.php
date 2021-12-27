<?php

namespace AssegaiPHP\Modules\ModuleName;

use Assegai\Core\Attributes\Injectable;
use Assegai\Database\Attributes\Repository;
use Assegai\Database\BaseRepository;

#[Repository(
  entity: '',
  tableName: 'TableName'
)]
#[Injectable]
class ModuleNameRepository extends BaseRepository
{
}
