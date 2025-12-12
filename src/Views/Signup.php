<div class="center-form">
  <form method="POST" action="/signup">
    <h2 class="center">Signup</h2>
    <article class="alert danger"><?= $error ?? '' ?></article>
    <label>Username
      <input name="username" type="text" autocapitalize="off" required>
    </label>
    <label>Password <span class="toggle-password" style="cursor:pointer"><i class="bi bi-eye-slash"></i></span>
      <input name="password" type="password" required>
    </label>
    <label>Confirm Password <span class="toggle-password" style="cursor:pointer"><i class="bi bi-eye-slash"></i></span>
      <input name="confirmPass" type="password" required>
    </label>
    <button type="submit"><i class="bi bi-person-plus"></i> Signup</button>
  </form>
  <p>Already have an account? <a href="/login">Login here</a>.</p>
</div>
<script type="module">
  import { togglePasswordVisibility } from '/assets/utils.js'
  togglePasswordVisibility()
</script>