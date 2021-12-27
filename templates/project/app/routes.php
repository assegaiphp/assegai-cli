<?php

use Assegai\Core\Routing\Route;
use AssegaiPHP\Modules\Home\HomeModule;

return [
  new Route(path: '/', module: HomeModule::class),
];