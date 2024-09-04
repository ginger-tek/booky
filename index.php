<?php

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if (is_file(__DIR__ . $uri) && $uri != '/data.json')
  return false;

function csv($arr = [])
{
  if (empty($arr))
    return '';
  $cols = array_keys(array_filter(get_object_vars($arr[0]), fn($v) => !is_array($v)));
  $csv = [join(',', $cols)];
  foreach ($arr as $row)
    $csv[] = join(',', array_map(fn($c) => is_numeric($row->{$c}) ? $row->{$c} : "\"{$row->{$c} }\"", $cols));
  return join("\n", $csv);
}

(match ($method . $uri) {
  'PUT/data' => function () {
      file_put_contents('data.json', file_get_contents('php://input'));
      echo json_encode(['result' => true]);
    },
  'GET/data' => function () {
      echo @file_get_contents('data.json') ?: json_encode(['invoices' => [], 'clients' => [], 'template' => '[invoice.summary]']);
    },
  'GET/export/csv' => function () {
      $data = json_decode(file_get_contents('data.json'));
      $invoices = csv($data->invoices);
      $clients = csv($data->clients);
      $expenses = csv(...array_map(fn($i) => array_values(array_filter($i->items, fn($e) => (bool) $e->purchaseDate)), $data->invoices));
      $path = 'booky.zip';
      $zip = new ZipArchive;
      $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
      $zip->addFromString('invoices.csv', $invoices);
      $zip->addFromString('clients.csv', $clients);
      $zip->addFromString('expenses.csv', $expenses);
      $zip->addFromString('template.html', $data->template);
      $zip->close();
      header('content-type: application/zip');
      echo file_get_contents($path);
      unlink($path);
    },
  default => function () {
      include 'src/app.html';
    }
})();