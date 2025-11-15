<?php

namespace App\Controllers;

use GingerTek\Routy;
use App\Services\Users;
use App\Services\Tokens;

class Auth
{
  public static function postLogin(Routy $app)
  {
    $body = $app->getBody();
    $user = (new Users)->find($body->username);
    if (!$user || !password_verify($body->password, $user->passhash))
      return $app->render('Login', [
        'error' => 'Invalid credentials'
      ]);
    [$token, $exp] = Tokens::encode([
      'uid' => $user->id,
      'name' => $user->username
    ], 3600);
    setcookie('token', $token, $exp);
    $app->redirect('/dashboard');
  }

  public static function viewLogin(Routy $app)
  {
    $users = (new Users)->list();
    $app->render('Login', ['users' => $users]);
  }

  public static function postSignup(Routy $app)
  {
    $body = $app->getBody();
    $svc = new Users;
    if ($exists = $svc->find($body->username))
      return $app->render('Signup', [
        'error' => 'Username taken'
      ]);
    $hash = password_hash($body->password, PASSWORD_BCRYPT);
    if ($user = $svc->create($body->username, $hash))
      return $app->redirect('/login');
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
