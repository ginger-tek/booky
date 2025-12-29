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
            <div class="overflow-auto" style="max-height:75dvh">
              <h4>Syntax</h4>
              <p>Use the <code>{{ token }}</code> syntax to render values for invoice/client/settings or plain strings into
                the template. If the token doesn't match any of the following, the string will be used as is.
              </p>
              <p>Available tokens:</p>
              <div class="grid sm">
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
              <article class="alert info"><code>itemizationsTable</code> is a special token that renders a pre-formatted
                table of the invoice items.</article>
              <h4>Formatting Modifiers</h4>
              <p>You can apply formatting to the token values or plain strings using the pipe (<code>|</code>) character,
                and an optional second argument, if applicable, prepended by a colon (<code>:</code>) character. The
                following formatting modifiers are available:</p>
              <ul>
                <li><code>lcase</code> - converts a string to lowercase</li>
                <li><code>ucase</code> - converts a string to uppercase</li>
                <li><code>pre</code> - formats a string with preserved whitespace and line breaks</li>
                <li><code>date</code> - formats a date string as a human-readable date; optional argument overrides the
                  default date format string (see <a href="https://www.php.net/manual/en/datetime.format.php"
                    target="_blank">PHP date format</a> for reference)</li>
                <li><code>currency</code> - formats a number as USD currency</li>
                <li><code>link</code> - formats a string as a clickable hyperlink; optional argument sets the visible link
                  text</li>
                <li><code>mailto</code> - formats a string as a clickable email link; optional argument sets the visible
                  link text</li>
                <li><code>tel</code> - formats a string as a clickable telephone link; optional argument sets the visible
                  link text</li>
              </ul>
              </p>
              <h5>Example</h5>
              <p><code>{{ invoice.dueDate|date:M j, Y }}</code> = <?= date('M j, Y') ?></p>
            </div>
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