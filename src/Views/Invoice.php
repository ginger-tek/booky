<?php
/** 
 * @var object $invoice
 * @var object[] $clients
 * @var object[] $items
 */
?>
<dialog id="newItem">
  <article>
    <header><b>New Item</b></header>
    <form method="POST" action="/invoices/<?= $invoice->id ?>/items">
      <label>Summary
        <input type="text" name="summary" autocomplete="off" required>
      </label>
      <label>Type
        <select name="type" required>
          <option value="">Select a type</option>
          <option value="service">Service</option>
          <option value="product">Product</option>
          <option value="expense">Expense</option>
        </select>
      </label>
      <label>Amount
        <input type="number" name="amount" value="0.00" step="0.01" min="0" required>
      </label>
      <div class="flex fill">
        <button type="button" class="secondary" onclick="newItem.close()">Cancel</button>
        <button type="submit">Add</button>
      </div>
    </form>
  </article>
</dialog>
<dialog id="confirmDelete">
  <article>
    <header><b>Confirm Delete</b></header>
    <p>This action cannot be undone. Are you sure you want to delete this invoice?</p>
    <form method="POST" id="delete" action="/invoices/<?= $invoice->id ?>/delete">
      <div class="flex fill">
        <button type="button" class="secondary" onclick="confirmDelete.close()">No</button>
        <button type="submit">Yes, Delete</button>
      </div>
    </form>
  </article>
</dialog>
<form id="invoiceForm" method="POST" action="/invoices/<?= $invoice->id ?>">
  <div class="flex fill bottom-spacing">
    <a role="button" class="secondary" href="/invoices">
      <i class="bi bi-arrow-left"></i> Back
    </a>
    <button type="submit"><i class="bi bi-floppy"></i> Save</button>
    <button type="button" id="previewBtn"><i class="bi bi-eye"></i> Preview</button>
    <button type="button" id="printBtn"><i class="bi bi-printer"></i> Print</button>
    <button type="button" class="danger" onclick="this.blur();confirmDelete.showModal()"><i class="bi bi-trash"></i>
      Delete</button>
  </div>
  <div class="grid">
    <label>Summary
      <input type="text" name="summary" value="<?= htmlspecialchars($invoice->summary) ?>" required>
    </label>
    <label>Client
      <select name="clientId" required>
        <option value="" disabled selected>Select a client</option>
        <?php foreach ($clients as $client): ?>
          <option value="<?= $client->id ?>" <?= $invoice->clientId === $client->id ? 'selected' : '' ?>>
            <?= htmlspecialchars($client->name) ?>
          </option>
        <?php endforeach ?>
      </select>
    </label>
  </div>
  <label>Details
    <textarea name="details" rows="4"><?= htmlspecialchars($invoice->details) ?></textarea>
  </label>
  <div class="grid">
    <label>Due Date
      <input type="date" name="dueDate"
        value="<?= $invoice->dueDate ? htmlspecialchars((new DateTime($invoice->dueDate))->format('Y-m-d')) : '' ?>">
    </label>
    <label>Paid Date
      <input type="date" name="paidDate"
        value="<?= $invoice->paidDate ? htmlspecialchars((new DateTime($invoice->paidDate))->format('Y-m-d')) : '' ?>">
    </label>
    <label>Amount Paid
      <input type="number" name="amountPaid" value="<?= htmlspecialchars($invoice->amountPaid) ?>" step="0.01" min="0"
        required>
    </label>
  </div>
</form>
<div class="flex spread bottom-spacing">
  <div>
    <h4 class="bottom-clear">Itemizations</h4>
    <p class="bottom-clear">Amount Due: <b><?= \App\Utils::currency($invoice->amountDue) ?></b></p>
  </div>
  <button onclick="this.blur();newItem.showModal()"><i class="bi bi-plus"></i> Add Item</button>
</div>
<?php if (empty($items)): ?>
  <article>
    <div align="center">
      <div style="font-size:2em"><i class="bi bi-slash-square"></i></div>
      <p>No items found</p>
    </div>
  </article>
<?php else: ?>
  <div class="overflow-auto bordered">
    <table>
      <thead>
        <tr>
          <th>Summary</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td>
              <input style="width:auto" type="text" name="summary" form="saveItem<?= $item->id ?>"
                value="<?= htmlspecialchars($item->summary) ?>" required>
            </td>
            <td>
              <select style="width:auto" name="type" form="saveItem<?= $item->id ?>" required>
                <option value="service" <?= $item->type === 'service' ? 'selected' : '' ?>>Service</option>
                <option value="product" <?= $item->type === 'product' ? 'selected' : '' ?>>Product</option>
                <option value="expense" <?= $item->type === 'expense' ? 'selected' : '' ?>>Expense</option>
              </select>
            </td>
            <td>
              <input type="number" style="width:auto" name="amount" form="saveItem<?= $item->id ?>"
                value="<?= $item->amount ?>" step="0.01" min="0" required>
            </td>
            <td>
              <form method="POST" id="saveItem<?= $item->id ?>"
                action="/invoices/<?= $invoice->id ?>/items/<?= $item->id ?>"></form>
              <form method="POST" id="deleteItem<?= $item->id ?>"
                action="/invoices/<?= $invoice->id ?>/items/<?= $item->id ?>/delete"></form>
              <div class="flex">
                <button type="submit" form="saveItem<?= $item->id ?>"><i class="bi bi-floppy"></i> Save</button>
                <button type="submit" form="deleteItem<?= $item->id ?>" class="danger"><i class="bi bi-trash"></i>
                  Delete</button>
              </div>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
<?php endif ?>
<script type="module">
  import { ctrlSave, printInvoice } from '/assets/utils.js'
  ctrlSave(document.getElementById('invoiceForm'))
  previewBtn.onclick = () => printInvoice('<?= $invoice->id ?>')
  printBtn.onclick = () => printInvoice('<?= $invoice->id ?>', true)
</script>