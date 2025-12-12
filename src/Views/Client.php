<dialog id="confirmDelete">
  <article>
    <header><b>Confirm Delete</b></header>
    <p>This action cannot be undone. Are you sure you want to delete this client?</p>
    <form method="POST" id="delete" action="/clients/<?= $client->id ?>/delete">
      <div class="flex fill">
        <button type="button" class="secondary" onclick="confirmDelete.close()">No</button>
        <button type="submit">Yes, Delete</button>
      </div>
    </form>
  </article>
</dialog>
<form method="POST" action="/clients/<?= $client->id ?>">
  <div class="flex fill bottom-spacing">
    <a role="button" class="secondary" href="/clients">
      <i class="bi bi-arrow-left"></i> Back
    </a>
    <button type="submit"><i class="bi bi-floppy"></i> Save</button>
    <button type="button" class="danger" onclick="this.blur();confirmDelete.showModal()"><i class="bi bi-trash"></i> Delete</button>
  </div>
  <div class="grid">
    <label>Name
      <input type="text" name="name" value="<?= htmlspecialchars($client->name) ?>" required>
    </label>
    <label>Email
      <input type="email" name="email" value="<?= htmlspecialchars($client->email) ?>" required>
    </label>
    <label>Phone
      <input type="tel" name="phone" value="<?= htmlspecialchars($client->phone) ?>">
    </label>
  </div>
  <label>Address
    <textarea name="address" rows="4"><?= htmlspecialchars($client->address) ?></textarea>
  </label>
</form>