<?php

namespace App\Data;

class Database
{
  private \PDO $pdo;

  public function __construct()
  {
    $this->pdo = new \PDO('sqlite:../app.db', null, null, [
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ]);
  }

  public function exec(string $sql): void
  {
    $this->pdo->exec($sql);
  }

  public function run(string $sql, ?array $vals = []): \PDOStatement
  {
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($vals);
    return $stmt;
  }
}
