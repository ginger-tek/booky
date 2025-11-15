<?php

namespace App\Services;

class Users extends Service {
  public function create(string $username, string $passhash): ?object
  {
    $this->db->run("insert into users(username,passhash)
    values(?,?)", [
      $username,
      $passhash
    ]);
    return $this->get($this->db->lastInsertId());
  }

  public function get(int|string $id): ?object
  {
    return $this->db->run("select *
    from users
    where id = ?", [$id])->fetch() ?: null;
  }

  public function find(string $username): ?object {
    return $this->db->run("select *
    from users
    where username = ?", [$username])->fetch() ?: null;
  }

  public function list(): array
  {
    return $this->db->run("select *
    from users")->fetchAll();
  }
}
