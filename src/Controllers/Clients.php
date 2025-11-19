<?php

namespace App\Controllers;

use GingerTek\Routy;
use App\Services\Clients as CliService;

class Clients
{
  public static function routes(Routy $app)
  {
    $app->post('/', self::postCreate(...));
    $app->get('/', self::viewList(...));
    $app->get('/:id', self::viewOne(...));
    $app->post('/:id', self::postSaveOne(...));
    $app->post('/:id/delete', self::postDeleteOne(...));
  }

  public static function postCreate(Routy $app)
  {
    $data = $app->getBody();
    $client = (new CliService)->create(
      (string) $data->name
    );
    $app->redirect("/clients/{$client->id}");
  }

  public static function viewList(Routy $app)
  {
    $items = (new CliService)->list();
    $app->render('Clients', [
      'items' => $items
    ]);
  }

  public static function viewOne(Routy $app)
  {
    $id = $app->getParam('id');
    $client = (new CliService)->get($id);
    if (!$client)
      return $app->render('NotFound', [
        'error' => 'Client not found'
      ]);
    $app->render('Client', [
      'client' => $client
    ]);
  }

  public static function postSaveOne(Routy $app)
  {
    $id = $app->getParam('id');
    $data = $app->getBody();
    (new CliService)->update(
      $id,
      [
        'name' => (string) $data->name,
        'email' => (string) $data->email,
        'phone' => (string) $data->phone,
        'address' => (string) $data->address
      ]
    );
    $app->redirect("/clients/{$id}");
  }

  public static function postDeleteOne(Routy $app)
  {
    $id = $app->getParam('id');
    (new CliService)->delete($id);
    $app->redirect("/clients");
  }
}
