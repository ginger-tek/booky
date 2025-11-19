<?php

namespace App\Services;

class Users extends Service
{
  public function create(string $username, string $passhash): ?object
  {
    $id = uniqid();
    $this->db->run("insert into users(id,username,passhash)
    values(?,?,?)", [
      $id,
      $username,
      $passhash
    ]);
    return $this->get($id);
  }

  public function get(string $id): ?object
  {
    return $this->db->run("select *
    from users
    where id = ?", [$id])->fetch() ?: null;
  }

  public function find(string $username): ?object
  {
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
