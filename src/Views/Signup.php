<div class="center-form">
  <form method="POST" action="/signup">
    <h2 class="center">Signup</h2>
    <article class="alert danger"><?= $error ?? '' ?></article>
    <label>Username
      <input name="username" type="text" autocapitalize="off" value="<?= $cache_username ?? '' ?>" required>
    </label>
    <article class="alert info">Password requirements:
      <ul style="color:inherit" class="bottom-clear">
        <li>12 or more characters long
        <li>Lowercase characters</li>
        <li>Uppercase characters</li>
        <li>Numbers</li>
        <li>Special characters</li>
      </ul>
    </article>
    <label>Password <span class="toggle-password" style="cursor:pointer"><i class="bi bi-eye-slash"></i></span>
      <input name="password" type="password" minlength="12" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{12,}$" required>
    </label>
    <label>Confirm Password <span class="toggle-password" style="cursor:pointer"><i class="bi bi-eye-slash"></i></span>
      <input name="confirmPass" type="password" minlength="12" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{12,}$" required>
    </label>
    <button type="submit"><i class="bi bi-person-plus"></i> Signup</button>
  </form>
  <p>Already have an account? <a href="/login">Login here</a>.</p>
</div>
<script type="module">
  import { togglePasswordVisibility } from '/assets/utils.js'
  togglePasswordVisibility()
</script>