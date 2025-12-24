<form method="post" action="/settings">
  <div class="flex spread bottom-spacing">
    <h1>Settings</h1>
    <button type="submit" style="width:auto"><i class="bi bi-floppy"></i> Save</button>
  </div>
  <article>
    <?php foreach ($settings as $setting):
      if ($setting->name == 'template'): ?>
        <dialog id="templateHelp">
          <article>
            <header>
              <span class="close" aria-label="Close" onclick="templateHelp.close()"></span>
              <b>Template Help</b>
            </header>
            <h4>Syntax</h4>
            <p>Use the <code>{{ token }}</code> syntax to render values for invoice, client, or settings into the template.
              The invoice and client objects will follow the <code>object.property</code> syntax, while the settings values
              will just be referenced by their name. Any invalid tokens will be replaced with an empty string.</p>
            <p>Available tokens:</p>
            <div class="grid">
              <ul>
                <li><code>invoice.id</code></li>
                <li><code>invoice.summary</code></li>
                <li><code>invoice.details</code></li>
                <li><code>invoice.created</code></li>
                <li><code>invoice.dueDate</code></li>
                <li><code>invoice.paidDate</code></li>
                <li><code>invoice.amountDue</code></li>
                <li><code>invoice.amountPaid</code></li>
                <li><code>invoice.subtotal</code></li>
                <li><code>itemizationsTable</code></li>
              </ul>
              <ul>
                <li><code>client.name</code></li>
                <li><code>client.email</code></li>
                <li><code>client.phone</code></li>
                <li><code>client.address</code></li>
                <li><code>company</code></li>
                <li><code>website</code></li>
                <li><code>email</code></li>
                <li><code>phone</code></li>
                <li><code>address</code></li>
              </ul>
            </div>
            <p><code>itemizationsTable</code> is a special token that renders a table of the invoice's items.</p>
            <h4>Modifiers</h4>
            <p>You can apply modifiers to the tokens using the pipe <code>|</code> character, and optional arguments
              separated by a colon <code>:</code>. The following modifiers are available:</p>
            <ul>
              <li><code>lcase</code> - converts a string to lowercase</li>
              <li><code>ucase</code> - converts a string to uppercase</li>
              <li><code>date</code> - formats a date string as a human-readable date</li>
              <li><code>currency</code> - formats a number as a currency (USD)</li>
              <li><code>link</code> - formats a string as a clickable hyperlink</li>
              <li><code>mailto</code> - formats a string as a clickable email link</li>
              <li><code>tel</code> - formats a string as a clickable telephone link</li>
            </ul>
            </p>
            <p>Example: <code>{{ invoice.dueDate|date:F j, Y }}</code> will render the invoice due date formatted a month
              name, date, and year date string.</p>
          </article>
        </dialog>
        <label>Template / Preview <span onclick="templateHelp.showModal()" aria-describedby="templateHelp">
            <i class="bi bi-question-circle"></i></span></label>
        <div class="grid">
          <textarea class="bottom-clear code" name="template" rows="15"
            onkeyup="clearTimeout(window.b);window.b = setTimeout(() => this.nextElementSibling.srcdoc=this.value, 300)"
            required><?= htmlspecialchars($setting->value) ?></textarea>
          <iframe srcdoc="<?= htmlspecialchars($setting->value) ?>"
            style="background:white;width:100%;height:100%"></iframe>
        </div>
      <?php elseif ($setting->name == 'address'): ?>
        <label>Address
          <textarea name="address"><?= htmlspecialchars($setting->value) ?></textarea>
        </label>
      <?php elseif ($setting->name == 'website'): ?>
        <label>Website
          <div role="group">
            <button style="padding-left:1rem;padding-right:1rem;pointer-events:none" class="secondary">https://</button>
            <input type="text" name="website" value="<?= htmlspecialchars($setting->value) ?>" required>
          </div>
        </label>
      <?php else: ?>
        <label><?= ucfirst($setting->name) ?>
          <input type="text" name="<?= $setting->name ?>" value="<?= htmlspecialchars($setting->value) ?>" required>
        </label>
      <?php endif;
    endforeach; ?>
  </article>
</form>
<script type="module">
  import { ctrlSave } from '/assets/utils.js'
  ctrlSave(document.querySelector('form'))
</script>