<?php

namespace App\Models;

class InvoiceListItem
{
  public int $id;
  public string $summary;
  public string $clientName;
  public float $amountDue;
  public string $dueDate;
}