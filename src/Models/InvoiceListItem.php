<?php

namespace App\Models;

class InvoiceListItem
{
  public string $id;
  public string $summary;
  public int $clientId;
  public string $clientName;
  public float $amountDue;
  public string $dueDate;
}