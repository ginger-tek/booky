<?php

namespace App\Middleware;

use GingerTek\Routy;
use App\Services\Tokens;
use App\Services\Users;
use App\Utils;

class Session
{
  private static string $secret = 'potatodemon';

  public static function init(Routy $app)
  {
    if ($token = $_COOKIE['token'] ?? null) {
      if ($parsed = Tokens::decode($token))
        $app->setCtx('session', $parsed);
    }
  }

  public static function id(Routy $app)
  {
    self::init($app);
    if (!($sess = $app->getCtx('session')))
      return $app->redirect('/login?next=' . urlencode($_SERVER['REQUEST_URI']));
    if (!($user = (new Users)->get($sess->uid))) {
      setcookie('token', '', time() - 60);
      return $app->end(401);
    }
    $app->setCtx('user', $user);
    Utils::reqSet('uid', $user->id);
  }
}
