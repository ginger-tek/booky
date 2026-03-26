<?php

namespace App\Services;

use App\Data\Database;

class Clients extends Service
{
  public function __construct(?Database $db = new Database)
  {
    parent::__construct($db);
  }

  public function create(string $name): ?object
  {
    $id = \App\Utils::createId();
    $this->db->run("insert into clients(id,name,userId)
    values(?,?,?)", [
      $id,
      $name,
      $this->uid
    ]);
    return $this->get($id);
  }

  public function get(string $id): ?object
  {
    return $this->db->run("select *
    from clients
    where id = ? and userId = ?", [
      $id,
      $this->uid
    ])->fetch() ?: null;
  }

  public function list(?array $opts = []): array
  {
    $opts = ['uid' => $this->uid] + $opts;
    return $this->db->run("select *
    from clients
    where userId = :uid", $opts)->fetchAll();
  }

  public function update(string $id, array $data): ?object
  {
    $params = ['id' => $id, 'uid' => $this->uid] + $data;
    $this->db->run("update clients set
      name = :name,
      email = :email,
      phone = :phone,
      address = :address,
      updated = current_timestamp
    where id = :id and userId = :uid",
      $params
    );
    return $this->get($id);
  }

  public function delete(string $id): bool
  {
    return $this->db->run("delete from clients
    where id = ? and userId = ?", [
      $id,
      $this->uid
    ])->rowCount() == 1;
  }
}
