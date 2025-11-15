<?php

namespace App\Controllers;

use GingerTek\Routy;
use App\Services\Invoices as InvService;

class Invoices
{
  public static function routes(Routy $app)
  {
    $app->get('/', self::viewList(...));
  }

  public static function viewList(Routy $app)
  {
    $items = (new InvService($app->getCtx('user')->id))->list();
    $app->render('Invoices', [
      'items' => $items
    ]);
  }
}
