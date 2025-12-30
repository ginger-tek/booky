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

  public function update(array $data, ?string $userId = null): ?object
  {
    $this->db->run("update users
    set username = ?,
    email = ?,
    updated = current_timestamp
    where id = ?", [
      $data['username'],
      $data['email'],
      $this->uid ?: $userId
    ]);
    return $this->get($userId ?: $this->uid, null);
  }

  public function updatePassword(string $passhash, ?string $userId = null): ?object
  {
    $this->db->run("update users
    set passhash = ?,
    updated = current_timestamp
    where id = ?", [
      $passhash,
      $userId ?: $this->uid
    ]);
    return $this->get($userId ?: $this->uid, null);
  }

  public function enable(?string $userId = null): bool
  {
    return $this->db->run("update users
    set enabled = 1,
    updated = current_timestamp
    where id = ?", [
      $userId ?: $this->uid
    ])->rowCount() == 1;
  }

  public function disable(?string $userId = null): bool
  {
    return $this->db->run("update users
    set enabled = 0,
    updated = current_timestamp
    where id = ?", [
      $userId ?: $this->uid
    ])->rowCount() == 1;
  }

  public function delete(?string $userId = null): bool
  {
    return $this->db->run("delete from users
    where id = ?", [
      $userId ?: $this->uid
    ])->rowCount() == 1;
  }
}
