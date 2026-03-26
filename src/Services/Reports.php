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
    where userId = ? and dueDate between ? and ?", [
        $this->uid,
        $start,
        $end
      ])->fetch();
    return (float) $result->revenue;
  }

  public function getMonthExpenses(string $month): float
  {
    $start = "$month-01";
    $end = date('Y-m-d', strtotime("$start +1 month"));
    $result = $this->db->run("
    select coalesce(sum(ii.amount),0) as totalExpenses
    from invoice_items ii
    join invoices i on i.id = ii.invoiceId
    where i.userId = ?
    and ii.created between ? and ?
    and ii.type in ('expense')", [
        $this->uid,
        $start,
        $end
      ])->fetch();
    return (float) $result->totalExpenses;
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
    and i.paidDate is not null
    and ii.type in ('expense')", [
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
    where userId = ? and dueDate between ? and ?", [
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
    where userId = ? and dueDate between ? and ?", [
        $this->uid,
        $start,
        $end
      ])->fetch();
    return (float) $result->revenue;
  }

  public function getYearToDateExpenses(string $year): float
  {
    $start = "$year-01-01";
    $end = date('Y-m-d', strtotime("$start +1 year"));
    $result = $this->db->run("
    select coalesce(sum(ii.amount),0) as totalExpenses
    from invoice_items ii
    join invoices i on i.id = ii.invoiceId
    where i.userId = ?
    and ii.created between ? and ?
    and ii.type in ('expense')", [
        $this->uid,
        $start,
        $end
      ])->fetch();
    return (float) $result->totalExpenses;
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
    and i.paidDate is not null
    and ii.type in ('expense')", [
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
    where userId = ? and dueDate between ? and ?", [
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
    where userId = ? and dueDate between ? and ?", [
        $this->uid,
        $start,
        $end
      ])->fetch();
    return (int) $result->clientCount;
  }

  public function getExpenseLedgerItems(string $month): array
  {
    $start = "$month-01";
    $end = date('Y-m-d', strtotime("$start +1 month"));
    return $this->db->run("select ii.id, ii.summary, ii.amount, ii.created, i.id as invoiceId, i.summary as invoiceSummary
    from invoice_items ii
    join invoices i on i.id = ii.invoiceId
    where i.userId = ?
    and ii.created between ? and ?
    and ii.type in ('expense')
    order by ii.created desc", [
        $this->uid,
        $start,
        $end
      ])->fetchAll();
  }

  public function getTotalExpenses(string $month): float
  {
    $start = "$month-01";
    $end = date('Y-m-d', strtotime("$start +1 month"));
    $result = $this->db->run("
    select coalesce(sum(ii.amount),0) as totalExpenses
    from invoice_items ii
    join invoices i on i.id = ii.invoiceId
    where i.userId = ?
    and ii.created between ? and ?
    and ii.type in ('expense')", [
        $this->uid,
        $start,
        $end
      ])->fetch();
    return (float) $result->totalExpenses;
  }

  public function getStatsByDate(string $start, string $end, string $groupBy): object
  {
    $groupBy = match ($groupBy) {
      'month' => "strftime('%Y-%m', dueDate)",
      'week' => "strftime('%Y-%W', dueDate)",
      default => "strftime('%Y-%m-%d', dueDate)",
    };
    $revenue = array_map(fn($row) => (float) $row->revenue, $this->db->run("select coalesce(sum(amountDue),0) as revenue
    from invoices
    where userId = ? and dueDate between ? and ?
    group by $groupBy", [
      $this->uid,
      $start,
      $end
    ])->fetchAll());

    $groupBy = match ($groupBy) {
      'month' => "strftime('%Y-%m', ii.created)",
      'week' => "strftime('%Y-%W', ii.created)",
      default => "strftime('%Y-%m-%d', ii.created)",
    };
    $expenses = array_map(fn($row) => (float) $row->totalExpenses, $this->db->run("
    select coalesce(sum(ii.amount),0) as totalExpenses
    from invoice_items ii
    join invoices i on i.id = ii.invoiceId
    where i.userId = ?
    and ii.created between ? and ?
    and ii.type in ('expense')
    group by $groupBy", [
      $this->uid,
      $start,
      $end
    ])->fetchAll());

    $groupBy = match ($groupBy) {
      'month' => "strftime('%Y-%m', i.paidDate)",
      'week' => "strftime('%Y-%W', i.paidDate)",
      default => "strftime('%Y-%m-%d', i.paidDate)",
    };
    $income = array_map(fn($row) => (float) $row->income, $this->db->run("
    select coalesce(case when i.amountPaid != 0 then i.amountPaid else i.amountDue end - sum(ii.amount),0) as income
    from invoice_items ii
    join invoices i on i.id = ii.invoiceId
    where i.userId = ?
    and ii.created between ? and ?
    and i.paidDate is not null
    and ii.type in ('expense')
    group by $groupBy", [
      $this->uid,
      $start,
      $end
    ])->fetchAll());

    return (object) [
      'revenue' => $revenue,
      'expenses' => $expenses,
      'income' => $income,
    ];
  }
}