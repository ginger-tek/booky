<?php

namespace App\Data;

class Database {
  private \PDO $pdo;

  public function __construct() {
    $this->pdo = new \PDO('sqlite:../app.db', null, null, [
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ]);
    $this->pdo->exec(<<<SQL
    pragma foreign_keys=on;
    create table if not exists users(
      id integer primary key autoincrement,
      username text not null unique,
      passhash text not null,
      created datetime default current_timestamp,
      updated datetime default current_timestamp
    );
    create table if not exists invoices(
      id integer primary key autoincrement,
      clientId integer not null,
      summary text not null,
      userId integer not null,
      details text,
      dueDate text,
      paidDate text,
      amountDue number default 0.00,
      amountPaid number default 0.00,
      created datetime default current_timestamp,
      updated datetime default current_timestamp
    );
    SQL);
  }

  public function lastInsertId(): mixed {
    return $this->pdo->lastInsertId();
  }

  public function run(string $sql, ?array $vals = []): \PDOStatement {
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($vals);
    return $stmt;
  }
}
