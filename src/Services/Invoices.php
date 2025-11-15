<?php

namespace App\Services;

use App\Data\Database;

class Invoices extends Service {
  private int $uid;

  public function __construct(int $userId, ?Database $db = new Database)
  {
    parent::__construct($db);
    $this->uid = $userId;
  }

  public function create(int $clientId, string $summary): ?object
  {
    $this->db->run("insert into invoices(clientId,summary,userId)
    values(?,?,?)", [
      $clientId,
      $summary,
      $this->uid
    ]);
    return $this->get($this->db->lastInsertId());
  }

  public function get(int|string $id): ?object
  {
    return $this->db->run("select *
    from invoices
    where id = ? and userId = ?", [
      $id,
      $this->uid
    ])->fetch() ?: null;
  }

  public function list(?array $opts = []): array
  {
    $opts = ['uid' => $this->uid, ...$opts];
    return $this->db->run("select *,
      '' as clientName
    from invoices
    where userId = :uid", $opts)->fetchAll();
  }
}
