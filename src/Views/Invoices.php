<?php
/** 
 * @var \App\Models\InvoiceListItem[] $items 
 * @var object[] $clients
 */
?>
<dialog id="newInvoice">
  <article>
    <header><b>New Invoice</b></header>
    <form method="POST" action="/invoices">
      <label>Client
        <select name="clientId" required>
          <option value="" disabled selected>Select a client</option>
          <?php foreach ($clients as $client): ?>
            <option value="<?= $client->id ?>"><?= htmlspecialchars($client->name) ?></option>
          <?php endforeach ?>
        </select>
      </label>
      <label>Summary
        <input type="text" name="summary" autocomplete="off" required>
      </label>
      <div class="flex fill">
        <button type="button" class="secondary" onclick="newInvoice.close()">Cancel</button>
        <button type="submit">Create</button>
      </div>
    </form>
  </article>
</dialog>
<div class="flex spread bottom-spacing">
  <h2>Invoices</h2>
  <button onclick="this.blur();newInvoice.showModal()"><i class="bi bi-plus"></i></button>
</div>
<?php if (empty($items)): ?>
  <div align="center">
    <br>
    <div style="font-size:2em"><i class="bi bi-slash-square"></i></div>
    <p>No invoices found</p>
  </div>
<?php endif ?>
<?php foreach ($items as $item): ?>
  <article>
    <div class="flex spread">
      <div>
        <h3><?= $item->summary ?></h3>
        <div><i class="bi bi-person-fill"></i> <?= $item->clientName ?></div>
      </div>
      <div align="right">
        <h4><?= \App\Utils::currency($item->amountDue) ?></h4>
        <div><i class="bi bi-alarm"></i> Due <?= \App\Utils::slashDate($item->dueDate) ?: 'TBD' ?></div>
      </div>
    </div>
    <a href="/invoices/<?= $item->id ?>" class="stretch"></a>
  </article>
<?php endforeach ?>