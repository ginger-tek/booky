<?php

require 'vendor/autoload.php';

use GingerTek\Routy;
include 'classes.php';

session_start(['read_and_close' => true]);

$app = new Routy;

$app->post('/signup', function () use ($app) {
  $body = $app->getBody();
  $users = json_decode(@file_get_contents('data/users.json') ?: '{}');
  $user = $users->{$body->username} ?? false;
  if ($body->password !== $body->confirm)
    $app->sendJson(['error' => 'Passwords do not match']);
  if ($user)
    $app->sendJson(['error' => 'Username taken']);
  else {
    $user = (object) [
      'password' => password_hash($body->password, PASSWORD_BCRYPT),
      'data' => 'data/' . uniqid() . '.db'
    ];
    $users->{$body->username} = $user;
    file_put_contents('data/users.json', json_encode($users));
    session_start();
    $_SESSION['user'] = (object) ['username' => $body->username];
    $_SESSION['db'] = $user->data;
    $app->sendJson($_SESSION['user']);
  }
});
$app->post('/login', function () use ($app) {
  $body = $app->getBody();
  $users = json_decode(@file_get_contents('data/users.json') ?: '{}');
  $user = $users->{$body->username} ?? false;
  if (!$user || !password_verify($body->password, $user->password))
    $app->sendJson(['error' => 'Username or password incorrect']);
  else {
    session_start();
    $_SESSION['user'] = (object) ['username' => $body->username];
    $_SESSION['db'] = $user->data;
    $app->sendJson($_SESSION['user']);
  }
});
$app->get('/session', fn() => $app->sendJson($_SESSION['user'] ?? null));
$app->get('/logout', Middleware::auth(...), function () use ($app) {
  session_start();
  session_destroy();
  $app->redirect('/');
});
$app->get('/data', Middleware::auth(...), function () use ($app) {
  $app->sendJson((new DB)->getData());
});
$app->put('/data', Middleware::auth(...), function () use ($app) {
  (new DB)->putData($app->getBody());
  $app->sendJson(['result' => true]);
});
$app->post('/data/:table', function () use ($app) {
  $db = new DB;
  $app->sendJson(match ($app->params->table) {
    'invoices' => $db->createInvoice($app->getBody()),
    'clients' => $db->createClient($app->getBody()),
    'items' => $db->createItem($app->getBody()),
    default => null
  });
});
$app->delete('/data/:table/:id', function () use ($app) {
  $db = new DB;
  $app->sendJson(match ($app->params->table) {
    'invoices' => $db->deleteInvoice($app->params->id),
    'clients' => $db->deleteClient($app->params->id),
    'items' => $db->deleteItem($app->params->id),
    default => null
  });
});
$app->get('/export', function () use ($app) {
  $db = new DB;
  $data = $db->getData();
  $invoices = Utils::toCsv($data->invoices);
  $clients = Utils::toCsv($data->clients);
  $items = Utils::toCsv($db->getItems());
  $path = uniqid(null, true);
  $zip = new ZipArchive;
  $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
  $zip->addFromString('invoices.csv', $invoices);
  $zip->addFromString('clients.csv', $clients);
  $zip->addFromString('items.csv', $items);
  $zip->addFromString('template.html', $data->template);
  $zip->addFile($_SESSION['db'], 'booky.db');
  $zip->close();
  $zip = file_get_contents($path);
  unlink($path);
  $app->sendData($zip, 'application/zip');
});

$app->group('/assets', fn() => $app->serveStatic('public'));
$app->get('/', fn() => $app->sendData('public/index.html'));
$app->end(404);