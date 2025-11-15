<?php

namespace App\Services;

use App\Data\Database;

class Service {
  protected Database $db;

  public function __construct(?Database $db = new Database) {
    $this->db = $db;
  }
}
