<?php

namespace App\Controllers;

use GingerTek\Routy;
use App\Services\Settings as SettingsService;

class Settings
{
  public static function routes(Routy $app)
  {
    $app->post('/', self::postSave(...));
    $app->get('/', self::view(...));
    $app->post('/:id/delete', self::postDeleteOne(...));
  }

  public static function postCreate(Routy $app)
  {
    $data = $app->getBody();
    (new SettingsService)->set(
      (string) $data->name,
      (string) $data->value
    );
    $app->redirect('/settings');
  }

  public static function view(Routy $app)
  {
    $settings = (new SettingsService)->list();
    $app->render('Settings', [
      'settings' => $settings
    ]);
  }

  public static function postSave(Routy $app)
  {
    $data = $app->getBody();
    (new SettingsService)->setBulk(
      [
        'company' => trim($data->company ?? ''),
        'website' => trim(str_replace('https://', '', $data->website ?? '')),
        'email' => trim($data->email ?? ''),
        'phone' => trim($data->phone ?? ''),
        'address' => trim($data->address ?? ''),
        'template' => $data->template ?? ''
      ]
    );
    $app->redirect('/settings');
  }

  public static function postDeleteOne(Routy $app)
  {
    $id = $app->getParam('id');
    (new SettingsService)->delete($id);
    $app->redirect("/settings");
  }
}
