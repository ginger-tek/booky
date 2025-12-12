<div class="center-form">
  <form method="POST" action="/login<?= $app->getQuery('next') ? '?next=' . urlencode($app->getQuery('next')) : '' ?>">
    <h2 class="center">Login</h2>
    <article class="alert danger"><?= $error ?? '' ?></article>
    <label>Username
      <input name="username" type="text" autocapitalize="off" required>
    </label>
    <label>Password <span class="toggle-password" style="cursor:pointer"><i class="bi bi-eye-slash"></i></span>
      <input name="password" type="password" required>
    </label>
    <button type="submit"><i class="bi bi-box-arrow-in-right"></i> Login</button>
  </form>
  <p>Don't have an account? <a href="/signup">Signup here</a>.</p>
</div>
<script type="module">
  import { togglePasswordVisibility } from '/assets/utils.js'
  togglePasswordVisibility()
</script>