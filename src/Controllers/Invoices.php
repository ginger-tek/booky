<?php

namespace App\Controllers;

use GingerTek\Routy;
use App\Data\Database;
use App\Services\Invoices as InvService;
use App\Services\Clients;
use App\Services\Settings;

class Invoices
{
  public static function routes(Routy $app)
  {
    $app->post('/', self::postCreate(...));
    $app->get('/', self::viewList(...));
    $app->get('/:id', self::viewOne(...));
    $app->post('/:id', self::postSaveOne(...));
    $app->post('/:id/delete', self::postDeleteOne(...));
    $app->get('/:id/print', self::viewPrint(...));
    $app->post('/:id/items', self::postCreateItem(...));
    $app->post('/:id/items/:itemId', self::postSaveItem(...));
    $app->post('/:id/items/:itemId/delete', self::postDeleteItem(...));
  }

  public static function postCreate(Routy $app)
  {
    $data = $app->getBody();
    if (empty($data->clientId))
      return $app->render('Error', [
        'error' => 'Client is required'
      ]);
    $invoice = (new InvService)->create(
      (string) $data->clientId,
      (string) $data->summary
    );
    $app->redirect("/invoices/{$invoice->id}");
  }

  public static function viewList(Routy $app)
  {
    $db = new Database;
    $items = (new InvService($db))->list();
    $clients = (new Clients($db))->list();
    $app->render('Invoices', [
      'items' => $items,
      'clients' => $clients
    ]);
  }

  public static function viewOne(Routy $app)
  {
    $id = $app->getParam('id');
    $db = new Database;
    $invSvc = new InvService($db);
    $invoice = $invSvc->get($id);
    if (!$invoice)
      return $app->render('NotFound', [
        'error' => 'Invoice not found'
      ]);
    $clients = (new Clients($db))->list();
    $items = $invSvc->listItems($id);
    $app->render('Invoice', [
      'invoice' => $invoice,
      'clients' => $clients,
      'items' => $items
    ]);
  }

  public static function viewPrint(Routy $app)
  {
    $id = $app->getParam('id');
    $db = new Database;
    $invSvc = new InvService($db);
    $settingsSvc = new Settings($db);
    $invoice = $invSvc->get($id);
    if (!$invoice)
      return $app->render('NotFound', [
        'error' => 'Invoice not found'
      ]);
    $client = (new Clients($db))->get($invoice->clientId);
    $items = $invSvc->listItems($id);
    ob_start();
    $settings = $settingsSvc->list(true);
    $template = $settings['template'] ?? '<h1>Invoice Template</h1>';
    $result = \App\Utils::parseTemplate($template, [
      'invoice' => $invoice,
      'client' => $client,
      'items' => $items,
      'settings' => $settings
    ]);
    exit($result);
  }

  public static function postSaveOne(Routy $app)
  {
    $id = $app->getParam('id');
    $data = $app->getBody();
    $invoice = (new InvService)->update(
      $id,
      [
        'summary' => (string) $data->summary,
        'clientId' => (string) $data->clientId,
        'details' => (string) $data->details,
        'dueDate' => (string) $data->dueDate,
        'amountPaid' => (float) $data->amountPaid,
        'paidDate' => (string) $data->paidDate
      ]
    );
    $app->redirect("/invoices/{$invoice->id}");
  }

  public static function postDeleteOne(Routy $app)
  {
    $id = $app->getParam('id');
    (new InvService)->delete($id);
    $app->redirect("/invoices");
  }

  public static function postCreateItem(Routy $app)
  {
    $id = $app->getParam('id');
    $data = $app->getBody();
    (new InvService)->createItem(
      $id,
      (string) $data->summary,
      (string) $data->type,
      (float) $data->amount
    );
    $app->redirect("/invoices/{$id}");
  }

  public static function postSaveItem(Routy $app)
  {
    $id = $app->getParam('id');
    $itemId = $app->getParam('itemId');
    $data = $app->getBody();
    (new InvService)->updateItem(
      $itemId,
      [
        'summary' => (string) $data->summary,
        'type' => (string) $data->type,
        'amount' => (float) $data->amount
      ]
    );
    $app->redirect("/invoices/{$id}");
  }

  public static function postDeleteItem(Routy $app)
  {
    $id = $app->getParam('id');
    $itemId = $app->getParam('itemId');
    (new InvService)->deleteItem($itemId);
    $app->redirect("/invoices/{$id}");
  }
}
