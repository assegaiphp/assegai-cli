<?php

namespace AssegaiPHP\Modules\ModuleName;

use Assegai\Database\Attributes\Entity;
use Assegai\Database\BaseEntity;

use AssegaiPHP\Resources\Values;

#[Entity(tableName: 'TableName', database: Values::DEFAULT_DB_NAME)]
class ClassNameEntity extends BaseEntity
{
}
