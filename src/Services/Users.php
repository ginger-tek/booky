<?php

namespace App\Services;

class Users extends Service
{
  private function enabledClause(?bool $flag = true): string
  {
    return $flag === true ? "and enabled = 1" : ($flag === false ? "and enabled = 0" : '');
  }

  public function create(string $username, string $email, string $passhash): ?object
  {
    $id = \App\Utils::createId();
    $this->db->run("insert into users(id,username,email,passhash)
    values(?,?,?,?)", [
      $id,
      $username,
      $email,
      $passhash
    ]);
    return $this->get($id, null);
  }

  public function get(string $id, ?bool $isEnabled = true): ?object
  {
    return $this->db->run("select *
    from users
    where id = ? " . $this->enabledClause($isEnabled), [$id])->fetch() ?: null;
  }

  public function find(string $username, ?bool $isEnabled = true): ?object
  {
    return $this->db->run("select *
    from users
    where username = ? " . $this->enabledClause($isEnabled), [$username])->fetch() ?: null;
  }

  public function list(?array $filters = []): array
  {
    $clauses = [];
    if (isset($filters['enabled']))
      $clauses[] = str_replace('and ', '', $this->enabledClause($filters['enabled'] ?? null));
    $clauses = count($clauses) > 0 ? 'where ' . join(' and ', $clauses) : '';
    return $this->db->run("select id, username, email, enabled, created, updated
    from users
    $clauses")->fetchAll();
  }

  public function update(string $userId, array $data): ?object
  {
    $this->db->run("update users
    set passhash = ?,
    email = ?,
    updated = current_timestamp
    where id = ?", [
      $data['passhash'],
      $data['email'],
      $userId
    ]);
    return $this->get($userId, null);
  }

  public function enable(string $userId): bool
  {
    return $this->db->run("update users
    set enabled = 1,
    updated = current_timestamp
    where id = ?", [
      $userId
    ])->rowCount() == 1;
  }

  public function disable(string $userId): bool
  {
    return $this->db->run("update users
    set enabled = 0,
    updated = current_timestamp
    where id = ?", [
      $userId
    ])->rowCount() == 1;
  }

  public function delete(string $userId): bool
  {
    return $this->db->run("delete from users
    where id = ?", [
      $userId
    ])->rowCount() == 1;
  }
}
