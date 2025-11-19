<?php

namespace App\Services;

use App\Data\Database;

class Service
{
  protected Database $db;
  protected string $uid;

  public function __construct(?Database $db = new Database)
  {
    $this->db = $db;
    $this->uid = \App\Utils::reqGet('uid') ?? '';
  }
}
