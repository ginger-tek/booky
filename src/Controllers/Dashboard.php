<?php

namespace App\Controllers;

use GingerTek\Routy;
use App\Data\Database;
use App\Services\Reports;

class Dashboard
{
  public static function view(Routy $app)
  {
    $db = new Database;
    $reportSvc = new Reports($db);
    $month = $app->getQuery('month') ?: date('Y-m');
    $year = $app->getQuery('year') ?: date('Y');
    $app->render('Dashboard', [
      'month' => $month,
      'revenue' => $reportSvc->getMonthRevenue($month),
      'expenses' => $reportSvc->getMonthExpenses($month),
      'income' => $reportSvc->getMonthIncome($month),
      'invoiceCount' => $reportSvc->getMonthInvoiceCount($month),
      'newClientCount' => $reportSvc->getMonthNewClientCount($month),
      'year' => $year,
      'revenueYTD' => $reportSvc->getYearToDateRevenue($year),
      'expensesYTD' => $reportSvc->getYearToDateExpenses($year),
      'incomeYTD' => $reportSvc->getYearToDateIncome($year),
      'invoiceCountYTD' => $reportSvc->getYearToDateInvoiceCount($year),
      'newClientCountYTD' => $reportSvc->getYearToDateNewClientCount($year),
    ]);
  }

  public static function viewExpenses(Routy $app)
  {
    $reportSvc = new Reports;
    $month = $app->getQuery('month') ?: date('Y-m');
    $app->render('Expenses', [
      'month' => $month,
      'expenses' => $reportSvc->getExpenseLedgerItems($month),
      'totalExpenses' => $reportSvc->getTotalExpenses($month),
    ]);
  }
}
