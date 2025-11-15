<?php

namespace App\Controllers;

use GingerTek\Routy;

class Dashboard
{
  public static function view(Routy $app)
  {
    $app->render('Dashboard', [
      'items' => []
    ]);
  }
}
