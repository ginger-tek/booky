<?php

namespace App\Services;

use App\Data\Database;

class Templates extends Service
{
  public function __construct(?Database $db = new Database)
  {
    parent::__construct($db);
  }

  public function init(?string $userId = null): void
  {
    if (!$this->uid && $userId !== null)
      $this->uid = $userId;
    $this->create('Default Template');
  }

  public function create(string $name, ?string $markup = null): ?object
  {
    $id = \App\Utils::createId();
    $markup ??= '<h1>Invoice Template</h1><div>Invoice Summary: {{ invoice.summary }}</div>{{ itemizationsTable }}';
    $this->db->run('insert into templates (id, name, markup, userId)
    VALUES (:id, :name, :markup, :userId)', [
      ':id' => $id,
      ':name' => $name,
      ':markup' => $markup,
      ':userId' => $this->uid
    ]);
    return $this->get($id);
  }

  public function list(): array
  {
    return $this->db->run('select id, name, isDefault, created, updated
    from templates
    where userId = :userId
    order by isDefault desc, created desc', [
      ':userId' => $this->uid
    ])->fetchAll();
  }

  public function get(string $id): ?object
  {
    return $this->db->run('select *
    from templates
    where id = :id
    and userId = :userId', [
      ':id' => $id,
      ':userId' => $this->uid
    ])->fetch() ?: null;
  }

  public function getDefault(): ?object
  {
    return $this->db->run('select *
    from templates
    where userId = :userId and isDefault = 1
    order by created
    limit 1', [
      ':userId' => $this->uid
    ])->fetch() ?: null;
  }

  public function update(string $id, ?array $data = []): void
  {
    if (isset($data['isDefault']) && $data['isDefault'] == 1)
      $this->db->run('update templates set isDefault = 0
      where userId = :userId', [
        ':userId' => $this->uid
      ]);
    $this->db->run('update templates
    set name = :name,
    isDefault = :isDefault,
    markup = :markup,
    updated = current_timestamp
    where id = :id
    and userId = :userId', [
      ':id' => $id,
      ':name' => $data['name'],
      ':isDefault' => $data['isDefault'] ?? 0,
      ':markup' => $data['markup'],
      ':userId' => $this->uid
    ]);
  }

  public function delete(string $id): void
  {
    $this->db->run('delete from templates
    where id = :id
    and userId = :userId', [
      ':id' => $id,
      ':userId' => $this->uid
    ]);
  }
}