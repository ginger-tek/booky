<?php

namespace App\Services;

use App\Data\Database;

class Invoices extends Service
{
  public function __construct(?Database $db = new Database)
  {
    parent::__construct($db);
  }

  public function create(string $clientId, string $summary): ?object
  {
    $id = uniqid();
    $this->db->run("insert into invoices(id,userId,clientId,summary)
    values(?,?,?,?)", [
      $id,
      $this->uid,
      $clientId,
      $summary,
    ]);
    return $this->get($id);
  }

  public function get(string $id): ?object
  {
    return $this->db->run("select *
    from invoices
    where id = ? and userId = ?", [
      $id,
      $this->uid
    ])->fetch() ?: null;
  }

  /**
   * @return \App\Models\InvoiceListItem[]
   */
  public function list(?array $opts = []): array
  {
    $opts = ['uid' => $this->uid];
    $orderBy = '';
    if (isset($opts['orderBy'])) {
      [$col, $dir] = $opts['orderBy'];
      $orderBy = "order by i.$col $dir";
    }
    if (isset($opts['limit'])) {
      $opts['limit'] = (int) $opts['limit'];
      $orderBy .= " limit :limit";
    }
    return $this->db->run("select 
      i.id,
      i.summary,
      c.name as clientName,
      i.amountDue,
      i.dueDate
    from invoices i
    left join clients c on c.id = i.clientId
    where i.userId = :uid
    $orderBy", $opts)->fetchAll();
  }

  public function update(string $id, array $data): ?object
  {
    $params = ['id' => $id, 'uid' => $this->uid] + $data;
    $this->db->run("update invoices set
      summary = :summary,
      clientId = :clientId,
      details = :details,
      dueDate = :dueDate,
      amountDue = :amountDue,
      amountPaid = :amountPaid,
      paidDate = :paidDate,
      updated = current_timestamp
    where id = :id and userId = :uid",
      $params
    );
    return $this->get($id);
  }

  public function delete(string $id): bool
  {
    return $this->db->run("delete from invoices
    where id = ? and userId = ?", [
      $id,
      $this->uid
    ])->rowCount() == 1;
  }

  public function createItem(string $invoiceId, string $summary, string $type, float $amount): ?object
  {
    $id = uniqid();
    $this->db->run("insert into invoice_items(id,userId,invoiceId,summary,type,amount)
    values(?,?,?,?,?,?)", [
      $id,
      $this->uid,
      $invoiceId,
      $summary,
      $type,
      $amount
    ]);
    return $this->getItem($id);
  }

  public function getItem(string $id): ?object
  {
    return $this->db->run("select *
    from invoice_items
    where id = ? and userId = ?", [
      $id,
      $this->uid
    ])->fetch() ?: null;
  }

  public function listItems(string $invoiceId): array
  {
    return $this->db->run("select *
    from invoice_items
    where invoiceId = ? and userId = ?", [
      $invoiceId,
      $this->uid
    ])->fetchAll();
  }

  public function updateItem(string $id, array $data): ?object
  {
    $params = ['id' => $id, 'uid' => $this->uid] + $data;
    $this->db->run("update invoice_items set
      summary = :summary,
      type = :type,
      amount = :amount,
      updated = current_timestamp
    where id = :id and userId = :uid",
      $params
    );
    return $this->getItem($id);
  }

  public function deleteItem(string $id): void
  {
    $this->db->run("delete from invoice_items
    where id = ? and userId = ?", [
      $id,
      $this->uid
    ]);
  }
}