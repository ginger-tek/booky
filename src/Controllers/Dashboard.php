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
      'income' => $reportSvc->getMonthIncome($month),
      'invoiceCount' => $reportSvc->getMonthInvoiceCount($month),
      'newClientCount' => $reportSvc->getMonthNewClientCount($month),
      'year' => $year,
      'revenueYTD' => $reportSvc->getYearToDateRevenue($year),
      'incomeYTD' => $reportSvc->getYearToDateIncome($year),
      'invoiceCountYTD' => $reportSvc->getYearToDateInvoiceCount($year),
      'newClientCountYTD' => $reportSvc->getYearToDateNewClientCount($year),
    ]);
  }
}
