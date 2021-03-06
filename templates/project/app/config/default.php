<?php

return [
  'app_name'        => 'NAME',
  'version'         => 'VERSION',
  'description'     => 'DESCRIPTION',
  'company_name'    => 'MY_COMPANY',
  'default_password_hash_algo' => PASSWORD_DEFAULT,
  'databases' => [
    'mysql' => [
      'db_name' => [
        'host'      => 'localhost',
        'user'      => '',
        'password'  => '',
        'name'      => 'db_name',
        'port'      => 3306,
      ]
    ],
    'pgsql' => [
      'db_name' => [
        'host'      => 'localhost',
        'user'      => '',
        'password'  => '',
        'name'      => 'db_name',
        'port'      => 5432,
      ]
    ],
    'sqlite' => [
      'db_name' => [
        'path' => '.data/db_name.sq3'
      ]
    ],
    'mongodb' => [],
  ],
  'request' => [
    'DEFAULT_LIMIT' => 100,
    'DEFAULT_SKIP'  => 0,
  ],
  'authentication' => [
    'secret' => 'SECRET_KEY',
    'strategies' => [
      'local' => Assegai\Lib\Authentication\Strategies\LocalStrategy::class,
      'jwt'   => Assegai\Lib\Authentication\Strategies\JWTStrategy::class,
      'oauth' => Assegai\Lib\Authentication\Strategies\OAuthStrategy::class,
    ],
    'default_strategy' => 'local',
    'jwt' => [
      'audience'                => 'https://yourdomain.com',
      'issuer'                  => 'assegai',
      'lifespan'                => '1 hour',
      'entityName'              => 'user',
      'entityClassName'         => AssegaiPHP\Modules\Users\UserEntity::class,
      'entityIdFieldname'       => 'email',
      'entityPasswordFieldname' => 'password',
    ]
  ]
];
