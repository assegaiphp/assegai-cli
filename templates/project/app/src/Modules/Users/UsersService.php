<?php

namespace AssegaiPHP\Modules\Users;

use Assegai\CLI\LIB\Util\Config;
use Assegai\Core\Attributes\Injectable;
use Assegai\Core\Result;
use Assegai\Database\Repository;
use Assegai\Database\Attributes\InjectRepository;
use Assegai\Database\Interfaces\IEntity;

use AssegaiPHP\Modules\Users\DTO\CreateUserDto;
use AssegaiPHP\Modules\Users\DTO\UpdateUserDto;
use AssegaiPHP\Modules\Users\Entities\UserEntity;

#[Injectable]
class UsersService
{
  public function __construct(
    #[InjectRepository(UserEntity::class)]
    private Repository $usersRepository
  )
  { }

  public function create(CreateUserDto $createUserDto)
  {
    $newUser = $this->usersRepository->create($createUserDto);

    $newUser->password = $this->hashPassword($newUser->password);

    // Validate and filter the new user here

    return $this->usersRepository->save();
  }

  public function findAll()
  {
    return $this->usersRepository->findAll();
  }
  
  public function findOne(int $id)
  {
    return $this->usersRepository->findOne(id: $id);
  }
  
  public function update(int $id, UpdateUserDto $updateUserDto)
  {
    return $this->usersRepository->update(conditions: ['id' => $id], entity: $updateUserDto);
  }

  public function remove(int $id)
  {
    return $this->usersRepository->softRemove(['id' => $id]);
  }

  private function hashPassword(string $password): string
  {
    $hashedPassword = password_hash($password, Config::get('default_password_hash_algo'));

    return $hashedPassword;
  }

  /**
   * Checks if the given password matches the given hash.
   * 
   * @return bool Returns TRUE if the password and hash match, or FALSE otherwise.
   */
  public function passwordsMatch(string $password, string $hash): bool
  {
    return password_verify($password, $hash);
  }
}
