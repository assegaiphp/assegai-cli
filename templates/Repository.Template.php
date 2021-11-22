<?php

namespace Assegai\CLI\Modules\ModuleName;

use Assegai\CLI\Core\Attributes\Injectable;
use Assegai\CLI\Database\Attributes\Repository;
use Assegai\CLI\Database\BaseRepository;

#[Repository(
  entity: '',
  tableName: 'TableName'
)]
#[Injectable]
class ModuleNameRepository extends BaseRepository
{
}

?>