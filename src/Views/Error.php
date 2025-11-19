<h2>Error</h2>
<?php if ($error ?? false): ?>
  <div><b>Details</b></div>
  <p><code><?= $error ?></code></p>
<?php endif ?>