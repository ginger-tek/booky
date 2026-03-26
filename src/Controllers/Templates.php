<?php

namespace App\Controllers;

use GingerTek\Routy;
use App\Services\Templates as TemplatesService;

class Templates
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
    $template = (new TemplatesService)->create(
      (string) $data->name
    );
    $app->redirect("/templates/{$template->id}");
  }

  public static function viewList(Routy $app)
  {
    $items = (new TemplatesService)->list();
    $app->render('Templates', [
      'items' => $items
    ]);
  }

  public static function viewOne(Routy $app)
  {
    $id = $app->getParam('id');
    $template = (new TemplatesService)->get($id);
    if (!$template)
      return $app->render('NotFound', [
        'error' => 'Template not found'
      ]);
    $app->render('Template', [
      'template' => $template
    ]);
  }

  public static function postSaveOne(Routy $app)
  {
    $id = $app->getParam('id');
    $data = $app->getBody();
    $template = (new TemplatesService)->update(
      $id,
      [
        'name' => (string) $data->name,
        'isDefault' => isset($data->isDefault) ? 1 : 0,
        'markup' => (string) $data->markup
      ]
    );
    if ($app->getHeader('Accept') === 'application/json')
      return $app->sendJson($template);
    $app->redirect("/templates/{$id}");
  }

  public static function postDeleteOne(Routy $app)
  {
    $id = $app->getParam('id');
    $svc = new TemplatesService;
    $svc->delete($id);
    $list = $svc->list();
    if (count($list) == 1) {
      $list[0]->isDefault = 1;
      $svc->update($list[0]->id, get_object_vars($list[0]));
    }
    $app->redirect("/templates");
  }
}
