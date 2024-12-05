<?php

require 'vendor/autoload.php';

use GingerTek\Routy;

session_start(['read_and_close' => true]);

function toCsv($arr = [])
{
  if (empty($arr))
    return '';
  $cols = array_keys(array_filter(get_object_vars($arr[0]), fn($v) => !is_array($v)));
  $csv = [join(',', $cols)];
  foreach ($arr as $row)
    $csv[] = join(',', array_map(fn($c) => is_numeric($row->{$c}) ? $row->{$c} : "\"{$row->{$c} }\"", $cols));
  return join("\n", $csv);
}

function auth(Routy $app)
{
  if (!isset($_SESSION['user']))
    $app->end(401);
}

$app = new Routy;

$app->post('/signup', function () use ($app) {
  $body = $app->getBody();
  $users = json_decode(@file_get_contents('data/users.json') ?: '{}');
  $user = $users->{$body->username} ?? false;
  if ($user)
    $app->sendJson(['error' => 'Username taken']);
  else {
    session_start();
    $user = (object) [
      'password' => password_hash($body->password, PASSWORD_BCRYPT),
      'path' => 'data/' . uniqid() . '.json'
    ];
    $users->{$body->username} = $user;
    file_put_contents('data/users.json', json_encode($users));
    $_SESSION['user'] = (object) [
      'username' => $body->username,
      'path' => $user->path
    ];
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
    $_SESSION['user'] = (object) [
      'username' => $body->username,
      'path' => $user->path
    ];
    $app->sendJson($_SESSION['user']);
  }
});
$app->get('/session', fn() => $app->sendJson($_SESSION['user'] ?? null));
$app->get('/logout', auth(...), function () use ($app) {
  session_start();
  session_destroy();
  $app->redirect('/');
});
$app->get('/data', auth(...), function () use ($app) {
  $app->sendData(@file_get_contents($_SESSION['user']->path) ?: json_encode(['invoices' => [], 'clients' => [], 'template' => '[invoice.summary]']), 'application/json');
});
$app->put('/data', auth(...), function () use ($app) {
  file_put_contents($_SESSION['user']->path, json_encode($app->getBody()));
  $app->sendJson(['result' => true]);
});
$app->get('/export/csv', function () use ($app) {
  $data = json_decode(file_get_contents($_SESSION['user']->path));
  $invoices = toCsv($data->invoices);
  $clients = toCsv($data->clients);
  $expenses = toCsv(...array_map(fn($i) => array_values(array_filter($i->items, fn($e) => (bool) $e->purchaseDate)), $data->invoices));
  $path = uniqid(null, true);
  $zip = new ZipArchive;
  $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
  $zip->addFromString('invoices.csv', $invoices);
  $zip->addFromString('clients.csv', $clients);
  $zip->addFromString('expenses.csv', $expenses);
  $zip->addFromString('template.html', $data->template);
  $zip->close();
  $zip = file_get_contents($path);
  unlink($path);
  $app->sendData($zip, 'application/zip');
});

$app->group('/assets', fn() => $app->serveStatic('public'));
$app->get('/', fn() => $app->sendData('public/index.html'));
$app->end(404);