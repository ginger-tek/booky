<?php

namespace App\Data;

class Database
{
  private \PDO $pdo;

  public function __construct()
  {
    $driver = getenv('DB_DRIVER') ?: 'sqlite';
    $dsn = getenv('DB_DSN') ?: (ROOT . 'app.db');
    $user = getenv('DB_USER') ?: null;
    $pass = getenv('DB_PASS') ?: null;
    $this->pdo = new \PDO("$driver:$dsn", $user, $pass, [
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
