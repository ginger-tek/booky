<?php
/**
 * @var string $month
 * @var float $revenue
 * @var float $expenses
 * @var float $income
 * @var int $invoiceCount
 * @var int $newClientCount
 * @var string $year
 * @var float $revenueYTD
 * @var float $expensesYTD
 * @var float $incomeYTD
 * @var int $invoiceCountYTD
 * @var int $newClientCountYTD
 */
?>
<form action="/dashboard" id="report"></form>
<div class="flex spread bottom-spacing">
  <h3>Monthly Stats</h3>
  <input type="month" name="month" form="report" style="width:auto" onchange="report.submit()" value="<?= $month ?>">
</div>
<div class="grid">
  <article align="center">
    <label>Revenue</label>
    <h2><?= \App\Utils::currency($revenue) ?></h2>
  </article>
  <article align="center">
    <label>Expenses</label>
    <h2><?= \App\Utils::currency($expenses) ?></h2>
  </article>
  <article align="center">
    <label>Income</label>
    <h2><?= \App\Utils::currency($income) ?></h2>
  </article>
</div>
<div class="grid">
  <article align="center">
    <label># of Invoices</label>
    <h2><?= $invoiceCount ?></h2>
  </article>
  <article align="center">
    <label># of New Clients</label>
    <h2><?= $newClientCount ?></h2>
  </article>
</div>
<div class="flex spread bottom-spacing">
  <h3>Annual Stats (<?= $year ?>)</h3>
  <select name="year" form="report" style="width:auto" onchange="report.submit()">
    <?php $c = date('Y');
    for ($y = $c; $y >= $c - 10; $y--): ?>
      <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
    <?php endfor ?>
  </select>
</div>
<div class="grid">
  <article align="center">
    <label>Revenue</label>
    <h2><?= \App\Utils::currency($revenueYTD) ?></h2>
  </article>
  <article align="center">
    <label>Expenses</label>
    <h2><?= \App\Utils::currency($expensesYTD) ?></h2>
  </article>
  <article align="center">
    <label>Income</label>
    <h2><?= \App\Utils::currency($incomeYTD) ?></h2>
  </article>
</div>
<div class="grid">
  <article align="center">
    <label># of Invoices</label>
    <h2><?= $invoiceCountYTD ?></h2>
  </article>
  <article align="center">
    <label># of New Clients</label>
    <h2><?= $newClientCountYTD ?></h2>
  </article>
</div>