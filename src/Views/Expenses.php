<form action="/expenses" id="ledger"></form>
<div class="flex spread bottom-spacing">
  <div>
    <h3 class="bottom-clear">Expenses</h3>
    <b>Total: <?= \App\Utils::currency($totalExpenses) ?></b>
  </div>
  <input type="month" name="month" form="ledger" style="width:auto" onchange="ledger.submit()" value="<?= $month ?>">
</div>
<?php if (!empty($expenses)): ?>
  <input id="search" type="search" placeholder="Filter expenses...">
<?php endif ?>
<div class="overflow-auto bordered">
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Summary</th>
        <th>Invoice</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($expenses)): ?>
        <tr>
          <td colspan="3">
            <div style="text-align:center">No expenses</div>
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($expenses as $expense): ?>
          <tr>
            <td><?= \App\Utils::slashDate($expense->created) ?></td>
            <td><?= htmlspecialchars($expense->summary) ?></td>
            <td>
              <?php if (!empty($expense->invoiceId)): ?>
                <a href="/invoices/<?= $expense->invoiceId ?>"><?= htmlspecialchars($expense->invoiceSummary) ?></a>
              <?php else: ?>
                N/A
              <?php endif ?>
            </td>
            <td><?= \App\Utils::currency($expense->amount) ?></td>
          </tr>
        <?php endforeach ?>
      <?php endif ?>
    </tbody>
  </table>
</div>
<?php if (!empty($expenses)): ?>
  <script type="module">
    import { initFilterChildElements } from '/assets/utils.js'
    initFilterChildElements(document.querySelector('table tbody'), search)
  </script>
<?php endif ?>