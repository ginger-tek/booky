<form method="post" action="/settings">
  <div class="flex spread bottom-spacing">
    <h1>Settings</h1>
    <button type="submit" style="width:auto"><i class="bi bi-floppy"></i> Save</button>
  </div>
  <article>
    <?php foreach ($settings as $setting):
      if (in_array($setting->name, ['template', 'address'])):
        if ($setting->name == 'template'): ?>
          <article class="alert info">
            Changes made here will only affect all future invoice template generations. Existing invoices will remain unchanged.
          </article>
        <?php endif; ?>
        <label><?= ucfirst($setting->name) ?>
          <textarea <?= $setting->name == 'template' ? 'class="code"' : '' ?> name="<?= $setting->name ?>"
            <?= $setting->name == 'template' ? 'rows="10" required' : '' ?>><?= htmlspecialchars($setting->value) ?></textarea>
        </label>
      <?php else: ?>
        <label><?= ucfirst($setting->name) ?>
          <?= $setting->name == 'website' ? '<div role="group"><button style="padding-left:1rem;padding-right:1rem;pointer-events:none" class="secondary">https://</button>' : '' ?>
          <input type="text" name="<?= $setting->name ?>" value="<?= htmlspecialchars($setting->value) ?>" required>
          <?= $setting->name == 'website' ? '</div>' : '' ?>
        </label>
      <?php endif;
    endforeach; ?>
  </article>
</form>
<script type="module">
  import { ctrlSave } from '/assets/utils.js'
  ctrlSave(document.querySelector('form'))
</script>