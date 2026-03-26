<?php

namespace App\Middleware;

use GingerTek\Routy;
use App\Services\Tokens;
use App\Services\Users;
use App\Utils;

class Session
{
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
    if (!($sess = $app->getCtx('session'))) {
      $params = !in_array($_SERVER['REQUEST_URI'], ['/', '/refresh', '/login', '/logout'])
        ? '?' . http_build_query(['next' => $_SERVER['REQUEST_URI']])
        : '';
      $app->uri == '/refresh'
        ? $app->end(401)
        : $app->redirect("/login$params");
    }
    if (!($user = (new Users)->get($sess->uid))) {
      setcookie('token', '', time() - 60);
      return $app->end(401);
    }
    $app->setCtx('user', $user);
    Utils::reqSet('uid', $user->id);
  }
}
