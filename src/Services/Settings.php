<?php

namespace App\Services;

use App\Data\Database;

class Settings extends Service
{
  public function __construct(?Database $db = new Database)
  {
    parent::__construct($db);
  }

  public function init(?string $userId = null): void
  {
    if (!$this->uid && $userId !== null)
      $this->uid = $userId;
    $this->setBulk([
      'company' => 'My Company',
      'email' => 'info@mycompany.com',
      'website' => 'https://mycompany.com',
      'phone' => '123-456-7890',
      'address' => '123 Main St, Anytown, USA',
      'template' => '<h1>Invoice Template</h1><div>Invoice Summary: {{ invoice.summary }}</div>{{ itemizationsTable }}'
    ]);
  }

  public function list(bool $asArray = false): array
  {
    $items = $this->db->run('SELECT * FROM settings WHERE userId = :userId', [
      ':userId' => $this->uid
    ])->fetchAll();
    if ($asArray) {
      $result = [];
      foreach ($items as $item)
        $result[$item->name] = $item->value;
      return $result;
    }
    return $items;
  }

  public function set(string $name, string $value): void
  {
    $this->db->run('insert or replace into settings (name, value, userId)
    VALUES (:name, :value, :userId)', [
      ':name' => $name,
      ':value' => $value,
      ':userId' => $this->uid
    ]);
  }

  public function setBulk(array $settings): void
  {
    foreach ($settings as $name => $value) {
      $this->set($name, $value);
    }
  }

  public function get(string $name): ?object
  {
    return $this->db->run('SELECT * FROM settings
    WHERE name = :name AND userId = :userId', [
      ':name' => $name,
      ':userId' => $this->uid
    ])->fetch() ?: null;
  }

  public function delete(string $name): void
  {
    $this->db->run('DELETE FROM settings
    WHERE name = :name AND userId = :userId', [
      ':name' => $name,
      ':userId' => $this->uid
    ]);
  }
}