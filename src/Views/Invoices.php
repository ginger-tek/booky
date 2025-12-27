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
          <option value="" disabled selected>
            <?= empty($clients) ? 'No clients available. Add a client first' : 'Select a client' ?>
          </option>
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
  <button onclick="this.blur();newInvoice.showModal()"><i class="bi bi-plus"></i> New Invoice</button>
</div>
<?php if (empty($items)): ?>
  <div align="center">
    <br>
    <div style="font-size:2em"><i class="bi bi-slash-square"></i></div>
    <p>No invoices yet</p>
  </div>
<?php else: ?>
  <input type="search" id="search" placeholder="Search invoices...">
  <div id="invoices">
    <?php foreach ($items as $item): ?>
      <article>
        <header>
          <h3 class="bottom-clear"><?= $item->summary ?></h3>
        </header>
        <h4 class="bottom-clear flex fill">
          <div class="center"><?= \App\Utils::currency($item->amountDue) ?></div>
          <div class="center"><i class="bi bi-person-square"></i> <?= $item->clientName ?></div>
          <div class="center"><i class="bi bi-calendar3"></i> <?= \App\Utils::slashDate($item->dueDate) ?: 'TBD' ?></div>
        </h4>
        <a href="/invoices/<?= $item->id ?>" class="stretch"></a>
      </article>
    <?php endforeach ?>
  </div>
  <script type="module">
    import { initFilterChildElements } from '/assets/utils.js'
    initFilterChildElements(invoices, search, 'No invoices found');
  </script>
<?php endif ?>