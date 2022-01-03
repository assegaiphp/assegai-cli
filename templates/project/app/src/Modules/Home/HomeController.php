<?php

namespace AssegaiPHP\Modules\Home;

use Assegai\Core\Config;
use Assegai\Core\Attributes\Controller;
use Assegai\Core\BaseController;
use Assegai\Core\Attributes\Get;
use Assegai\Core\RequestMethod;
use Assegai\Core\Responses\Response;

#[Controller(forbiddenMethods: [
  RequestMethod::DELETE,
  RequestMethod::HEAD,
  RequestMethod::PATCH,
  RequestMethod::POST,
  RequestMethod::PUT,
])]
class HomeController extends BaseController
{
  public function __construct(
    private HomeService $homeService
  ) { }

  #[Get]
  public function default(): Response
  {
    $data = [
      'name'        => Config::get('app_name'),
      'description' => Config::get('description'),
      'version'     => Config::get('version'),
      'copyright'   => '© ' . date('Y') . ' ' . Config::get('company_name'),
    ];

    return new Response( data: $data, dataOnly: true );
  }
}

?>