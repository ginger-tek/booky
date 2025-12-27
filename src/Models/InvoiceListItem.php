<?php

namespace App\Models;

class InvoiceListItem
{
  public int $id;
  public string $summary;
  public int $clientId;
  public string $clientName;
  public float $amountDue;
  public string $dueDate;
}