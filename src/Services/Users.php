<?php

namespace App\Services;

class Users extends Service
{
  public function create(string $username, string $passhash): ?object
  {
    $id = \App\Utils::createId();
    $this->db->run("insert into users(id,username,passhash)
    values(?,?,?)", [
      $id,
      $username,
      $passhash
    ]);
    return $this->get($id);
  }

  public function get(string $id, bool $isEnabled = true): ?object
  {
    return $this->db->run("select *
    from users
    where id = ? and enabled = " . ($isEnabled ? "1" : "0"), [$id])->fetch() ?: null;
  }

  public function find(string $username, bool $isEnabled = false): ?object
  {
    return $this->db->run("select *
    from users
    where username = ? " . ($isEnabled ? "and enabled = 1" : "and enabled = 0"), [$username])->fetch() ?: null;
  }

  public function list(): array
  {
    return $this->db->run("select *
    from users")->fetchAll();
  }
}
