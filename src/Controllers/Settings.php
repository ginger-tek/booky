<?php

namespace App\Controllers;

use GingerTek\Routy;
use App\Services\Users;

class Settings
{
  public static function routes(Routy $app)
  {
    $app->post('/update-user', self::postUpdateUser(...));
    $app->post('/update-password', self::postUpdatePassword(...));
    $app->get('/', self::view(...));
  }

  public static function view(Routy $app)
  {
    $app->render('Settings');
  }

  public static function postUpdateUser(Routy $app)
  {
    $data = $app->getBody();
    (new Users)->update([
      'email' => (string) $data->email
    ]);
    $app->redirect('/settings');
  }

  public static function postUpdatePassword(Routy $app)
  {
    $data = $app->getBody();
    $userSvc = new Users;
    $user = $userSvc->get($app->getCtx('user')->id);
    if (!$user || !password_verify($data->currentPassword ?? '', $user->passhash)) {
      return $app->render('Settings', [
        'error' => 'Current password is incorrect'
      ]);
    }
    if (($data->newPassword ?? '') !== ($data->confirmNewPass ?? '')) {
      return $app->render('Settings', [
        'error' => 'New passwords do not match'
      ]);
    }
    $newHash = password_hash($data->newPassword, PASSWORD_BCRYPT);
    $userSvc->updatePassword($newHash);
    $app->redirect('/settings');
  }
}
