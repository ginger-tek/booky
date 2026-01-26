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
  <input type="month" name="month" form="report" style="width:160px" onchange="report.submit()" value="<?= $month ?>">
</div>
<article style="position:relative;width:100%;height:30vh">
  <canvas id="monthChart"></canvas>
</article>
<div class="grid shrink">
  <article align="center" class="bg blue">
    <label>Revenue</label>
    <h2><?= \App\Utils::currency($revenue) ?></h2>
  </article>
  <article align="center" class="bg pink">
    <label>Expenses</label>
    <h2><?= \App\Utils::currency($expenses) ?></h2>
  </article>
  <article align="center" class="bg jade">
    <label>Income</label>
    <h2><?= \App\Utils::currency($income) ?></h2>
  </article>
</div>
<div class="flex spread bottom-spacing">
  <h3>Annual Stats</h3>
  <select name="year" form="report" style="width:auto" onchange="report.submit()">
    <?php $c = date('Y');
    for ($y = $c; $y >= $c - 10; $y--): ?>
      <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
    <?php endfor ?>
  </select>
</div>
<article style="position:relative;width:100%;height:30vh">
  <canvas id="yearChart"></canvas>
</article>
<div class="grid shrink">
  <article align="center" class="bg blue">
    <label>Revenue</label>
    <h2><?= \App\Utils::currency($revenueYTD) ?></h2>
  </article>
  <article align="center" class="bg pink">
    <label>Expenses</label>
    <h2><?= \App\Utils::currency($expensesYTD) ?></h2>
  </article>
  <article align="center" class="bg jade">
    <label>Income</label>
    <h2><?= \App\Utils::currency($incomeYTD) ?></h2>
  </article>
</div>
<script type="module">
  import 'https://unpkg.com/chart.js@4.5.1/dist/chart.umd.min.js';
  const style = window.getComputedStyle(document.documentElement);
  const revenueColor = style.getPropertyValue('--pico-blue').trim();
  const expensesColor = style.getPropertyValue('--pico-pink').trim();
  const incomeColor = style.getPropertyValue('--pico-jade').trim();
  const monthChartRef = new Chart(document.getElementById('monthChart'), {
    type: 'bar',
    data: {
      labels: ["<?= join('","', $monthWeeks) ?>"],
      datasets: [
        {
          label: 'Revenue',
          data: <?= json_encode($monthData->revenue) ?>,
          backgroundColor: revenueColor,
        },
        {
          label: 'Expenses',
          data: <?= json_encode($monthData->expenses) ?>,
          backgroundColor: expensesColor,
        },
        {
          label: 'Income',
          data: <?= json_encode($monthData->income) ?>,
          backgroundColor: incomeColor,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
  const yearChartRef = new Chart(document.getElementById('yearChart'), {
    type: 'line',
    data: {
      labels: ["<?= join('","', $yearMonths) ?>"],
      datasets: [
        {
          label: 'Revenue',
          data: <?= json_encode($yearData->revenue) ?>,
          backgroundColor: revenueColor,
        },
        {
          label: 'Expenses',
          data: <?= json_encode($yearData->expenses) ?>,
          backgroundColor: expensesColor,
        },
        {
          label: 'Income',
          data: <?= json_encode($yearData->income) ?>,
          backgroundColor: incomeColor,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>