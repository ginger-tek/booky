<?php

namespace App\Services;

use App\Data\Database;

class Reports extends Service
{
  public function __construct(?Database $db = new Database)
  {
    parent::__construct($db);
  }

  public function getMonthRevenue(string $month): float
  {
    $start = "$month-01";
    $end = date('Y-m-d', strtotime("$start +1 month"));
    $result = $this->db->run("select coalesce(sum(amountDue),0) as revenue
    from invoices
    where userId = ? and created between ? and ?", [
      $this->uid,
      $start,
      $end
    ])->fetch();
    return (float) $result->revenue;
  }

  public function getMonthIncome(string $month): float
  {
    $start = "$month-01";
    $end = date('Y-m-d', strtotime("$start +1 month"));
    $result = $this->db->run("
    select coalesce(i.amountPaid - sum(ii.amount),0) as income
    from invoice_items ii
    join invoices i on i.id = ii.invoiceId
    where i.userId = ?
    and ii.created between ? and ?
    and ii.type = 'product'", [
      $this->uid,
      $start,
      $end
    ])->fetch();
    return (float) $result->income;
  }

  public function getMonthInvoiceCount(string $month): int
  {
    $start = "$month-01";
    $end = date('Y-m-d', strtotime("$start +1 month"));
    $result = $this->db->run("select count(*) as invoiceCount
    from invoices
    where userId = ? and created between ? and ?", [
      $this->uid,
      $start,
      $end
    ])->fetch();
    return (int) $result->invoiceCount;
  }

  public function getMonthNewClientCount(string $month): int
  {
    $start = "$month-01";
    $end = date('Y-m-d', strtotime("$start +1 month"));
    $result = $this->db->run("select count(distinct clientId) as clientCount
    from invoices
    where userId = ? and created between ? and ?", [
      $this->uid,
      $start,
      $end
    ])->fetch();
    return (int) $result->clientCount;
  }

  public function getYearToDateRevenue(string $year): float
  {
    $start = "$year-01-01";
    $end = date('Y-m-d', strtotime("$start +1 year"));
    $result = $this->db->run("select coalesce(sum(amountDue),0) as revenue
    from invoices
    where userId = ? and created between ? and ?", [
      $this->uid,
      $start,
      $end
    ])->fetch();
    return (float) $result->revenue;
  }

  public function getYearToDateIncome(string $year): float
  {
    $start = "$year-01-01";
    $end = date('Y-m-d', strtotime("$start +1 year"));
    $result = $this->db->run("
    select coalesce(i.amountPaid - sum(ii.amount),0) as income
    from invoice_items ii
    join invoices i on i.id = ii.invoiceId
    where i.userId = ?
    and ii.created between ? and ?
    and ii.type in ('product','parts')", [
      $this->uid,
      $start,
      $end
    ])->fetch();
    return (float) $result->income;
  }

  public function getYearToDateInvoiceCount(string $year): int
  {
    $start = "$year-01-01";
    $end = date('Y-m-d', strtotime("$start +1 year"));
    $result = $this->db->run("select count(*) as invoiceCount
    from invoices
    where userId = ? and created between ? and ?", [
      $this->uid,
      $start,
      $end
    ])->fetch();
    return (int) $result->invoiceCount;
  }

  public function getYearToDateNewClientCount(string $year): int
  {
    $start = "$year-01-01";
    $end = date('Y-m-d', strtotime("$start +1 year"));
    $result = $this->db->run("select count(distinct clientId) as clientCount
    from invoices
    where userId = ? and created between ? and ?", [
      $this->uid,
      $start,
      $end
    ])->fetch();
    return (int) $result->clientCount;
  }
}