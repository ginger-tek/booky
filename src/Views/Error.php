<?php if (($error ?? false) && getenv('ENV') == 'dev'): ?>
  <h2>Error</h2>
  <pre><code><?= $error ?></code></pre>
<?php else: ?>
  <div align="center">
    <h2>Uh oh!</h2>
    <p>An unexpected error has ocurred during that action</p>
    <button onclick="history.back()"><i class="bi bi-arrow-left"></i> Go Back</button>
  </div>
<?php endif ?>