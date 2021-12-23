<?php

use Assegai\Core\Routing\Route;
use Assegai\Modules\Home\HomeModule;

return [
  new Route(path: '/', module: HomeModule::class),
];