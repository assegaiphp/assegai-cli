<?php

use AssegaiPHP\Modules\Authentication\AuthenticationModule;
use Assegai\Core\Routing\Route;
use AssegaiPHP\Modules\Home\HomeModule;

return [
  new Route(path: '/', module: HomeModule::class),
  new Route(path: 'authentication', module: AuthenticationModule::class),
];