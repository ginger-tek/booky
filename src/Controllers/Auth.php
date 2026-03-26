<?php

namespace App\Controllers;

use GingerTek\Routy;
use App\Services\Users;
use App\Services\Tokens;
use App\Services\Templates;
use App\Data\Database;

class Auth
{
  public static function postLogin(Routy $app)
  {
    $body = $app->getBody();
    if (!isset($body->username, $body->password))
      return $app->render('Login', [
        'error' => 'Missing required fields'
      ]);
    $user = (new Users)->find($body->username);
    if (!$user || !password_verify($body->password, $user->passhash))
      return $app->render('Login', [
        'error' => 'Invalid credentials'
      ]);
    [$token, $exp] = Tokens::encode([
      'uid' => $user->id,
      'name' => $user->username
    ]);
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
    if (!isset($body->username, $body->email, $body->password, $body->confirmPass))
      return $app->render('Signup', [
        'error' => 'Missing required fields'
      ]);
    $userSvc = new Users($db);
    if ($userSvc->find($body->username, null))
      return $app->render('Signup', [
        'error' => 'Username taken'
      ]);
    if ($body->password !== $body->confirmPass)
      return $app->render('Signup', [
        'cache_username' => $body->username,
        'cache_email' => $body->email,
        'error' => 'Passwords do not match'
      ]);
    $hash = password_hash($body->password, PASSWORD_BCRYPT);
    if ($user = $userSvc->create($body->username, $body->email, $hash)) {
      (new Templates($db))->init($user->id);
      return $app->redirect('/account-setup');
    }
    $app->render('Signup', [
      'error' => 'Failed to signup'
    ]);
  }

  public static function postExtendSession(Routy $app)
  {
    $session = $app->getCtx('session');
    if (!$session)
      return $app->end(401);
    [$token, $exp] = Tokens::encode([
      'uid' => $session->uid,
      'name' => $session->name
    ]);
    setcookie('token', $token, $exp);
    return $app->sendJson(['message' => 'Session extended', 'exp' => $exp]);
  }

  public static function viewSignup(Routy $app)
  {
    $app->render('Signup');
  }

  public static function viewSignupSuccess(Routy $app)
  {
    $app->render('SignupSuccess');
  }

  public static function getLogout(Routy $app)
  {
    setcookie('token', '', time() - 60);
    $app->redirect('/logged-out');
  }

  public static function viewLoggedOut(Routy $app)
  {
    $app->render('LoggedOut');
  }
}
