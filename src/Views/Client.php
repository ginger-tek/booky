<form method="POST" action="/clients/<?= $client->id ?>">
  <div class="flex fill bottom-spacing">
    <a role="button" class="secondary" href="/clients">
      <i class="bi bi-arrow-left"></i> Back
    </a>
    <button type="submit"><i class="bi bi-save"></i> Save</button>
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