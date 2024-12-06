<?php

use GingerTek\Routy;

function toCsv($arr = []): string
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

class DB
{
  protected PDO $db;
  function __construct()
  {
    $this->db = new PDO('sqlite:' . $_SESSION['db'], null, null, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    $this->db->exec(file_get_contents('schema.sql'));
  }

  function run(string $sql, array $values = []): PDOStatement
  {
    $stmt = $this->db->prepare($sql);
    $stmt->execute($values);
    return $stmt;
  }

  function getInvoices(): array
  {
    return $this->run("select * from invoices")->fetchAll();
  }

  function getInvoice(string $invoiceId): object
  {
    $invoice = $this->run("select * from invoices where id = ?", [$invoiceId])->fetch();
    $invoice->items = $this->getInvoiceItems($invoiceId);
    return $invoice;
  }

  function getClients(): array
  {
    return $this->run("select * from clients")->fetchAll();
  }

  function getClient(string $clientId): object
  {
    return $this->run("select * from clients where id = ?", [$clientId])->fetch();
  }

  function getItems(): array
  {
    return $this->run("select * from items")->fetchAll();
  }

  function getInvoiceItems(string $invoiceId): array
  {
    return $this->run("select * from items where invoiceId = ?", [$invoiceId])->fetchAll();
  }

  function getItem(string $itemId): object
  {
    return $this->run("select * from items where id = ?", [$itemId])->fetch();
  }

  function getExpenses(): array
  {
    return $this->run("select * from items where purchaseDate not null")->fetchAll();
  }

  function getTemplate(): ?string
  {
    return $this->run("select * from template")->fetch()->markup;
  }

  function putInvoice(object $obj)
  {
    foreach ($obj->items as $item)
      $this->run(
        "update items set summary = ?, type = ?, amount = ?, updated = current_timestamp where id = ?",
        [$item->summary, $item->type, $item->amount, $item->id]
      );
    $this->run(
      "update invoices set summary = ?, clientId = ?, details = ?, amountDue = ?, dueDate = ?, amountPaid = ?, paidDate = ?, updated = current_timestamp where id = ?",
      [$obj->summary, $obj->clientId, $obj->details, $obj->amountDue, $obj->dueDate, $obj->amountPaid, $obj->paidDate, $obj->id]
    );
  }

  function putClient(object $obj)
  {
    $this->run(
      "update clients set name = ?, email = ?, phone = ?, address = ?, company = ?, updated = current_timestamp where id = ?",
      [$obj->name, $obj->email, $obj->phone, $obj->address, $obj->company, $obj->id]
    );
  }

  function putTemplate(string $markup)
  {
    $this->run(
      "update template set markup = ?, updated = current_timestamp",
      [$markup]
    );
  }

  function createInvoice(object $obj): object
  {
    $this->run(
      "insert into invoices (summary,clientId) values(?,?)",
      [$obj->summary, $obj->clientId]
    );
    return $this->getInvoice($this->db->lastInsertId());
  }

  function createClient(object $obj): object
  {
    $this->run(
      "insert into clients (name) values (?)",
      [$obj->name]
    );
    return $this->getClient($this->db->lastInsertId());
  }

  function createItem(object $obj): object
  {
    $this->run(
      "insert into items (invoiceId,summary,type,amount,purchaseDate) values(?,?,?,?,?)",
      [$obj->invoiceId, $obj->summary, $obj->type, $obj->amount, $obj->purchaseDate]
    );
    return $this->getItem($this->db->lastInsertId());
  }

  function deleteInvoice(string $invoiceId): bool
  {
    return (bool) $this->run("delete from invoices where id = ?", [$invoiceId])->rowCount();
  }

  function deleteClient(string $clientId): bool
  {
    return (bool) $this->run("delete from clients where id = ?", [$clientId])->rowCount();
  }

  function deleteItem(string $itemId): bool
  {
    return (bool) $this->run("delete from items where id = ?", [$itemId])->rowCount();
  }

  function getData()
  {
    return (object) [
      'invoices' => array_map(fn($i) => $this->getInvoice($i->id), $this->getInvoices()),
      'clients' => $this->getClients(),
      'template' => $this->getTemplate()
    ];
  }

  function putData(object $obj)
  {
    foreach ($obj->invoices as $invoice)
      $this->putInvoice($invoice);
    foreach ($obj->clients as $client)
      $this->putClient($client);
    $this->putTemplate($obj->template);
  }
}