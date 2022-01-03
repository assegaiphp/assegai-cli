<?php

namespace AssegaiPHP\Modules\Home;

use Assegai\Core\Attributes\Module;
use Assegai\Core\BaseModule;

#[Module(
  controllers: [HomeController::class],
  providers: [HomeService::class]
)]
class HomeModule extends BaseModule
{
}

?>