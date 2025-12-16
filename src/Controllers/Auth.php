<?php

namespace App\Controllers;

use GingerTek\Routy;
use App\Services\Users;
use App\Services\Tokens;
use App\Services\Settings;
use App\Utils;
use App\Data\Database;

class Auth
{
  public static function postLogin(Routy $app)
  {
    $body = $app->getBody();
    $user = (new Users)->find($body->username, true);
    if (!$user || !password_verify($body->password, $user->passhash))
      return $app->render('Login', [
        'error' => 'Invalid credentials'
      ]);
    [$token, $exp] = Tokens::encode([
      'uid' => $user->id,
      'name' => $user->username
    ], 3600);
    setcookie('token', $token, $exp);
    $app->redirect($app->getQuery('next') ?: '/dashboard');
  }

  public static function viewLogin(Routy $app)
  {
    if ($app->getCtx('session'))
      return $app->redirect('/dashboard');
    $app->render('Login');
  }

  public static function postSignup(Routy $app)
  {
    $body = $app->getBody();
    $db = new Database;
    $userSvc = new Users($db);
    if ($userSvc->find($body->username, null))
      return $app->render('Signup', [
        'error' => 'Username taken'
      ]);
    if ($body->password !== $body->confirmPass)
      return $app->render('Signup', [
        'cache_username' => $body->username,
        'error' => 'Passwords do not match'
      ]);
    $hash = password_hash($body->password, PASSWORD_BCRYPT);
    if ($user = $userSvc->create($body->username, $hash)) {
      Utils::reqSet('uid', $user->id);
      (new Settings($db))->init();
      return $app->redirect('/login');
    }
    $app->render('Signup', [
      'error' => 'Failed to signup'
    ]);
  }

  public static function viewSignup(Routy $app)
  {
    $app->render('Signup');
  }

  public static function getLogout(Routy $app)
  {
    setcookie('token', '', time() - 60);
    $app->redirect('/login');
  }
}
