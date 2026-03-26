<div class="center-form">
  <form id="signupForm" method="POST" action="/signup">
    <h2 class="center">Signup</h2>
    <article class="alert danger"><?= $error ?? '' ?></article>
    <label>Username
      <input name="username" type="text" autocapitalize="off" value="<?= $cache_username ?? '' ?>" required>
    </label>
    <label>Email
      <input name="email" type="text" autocapitalize="off" value="<?= $cache_email ?? '' ?>" required>
    </label>
    <article class="alert info">Password must be 12 or more characters long, and include at least one of each of the following:
      <ul style="color:inherit" class="bottom-clear">
        <li>Lowercase character</li>
        <li>Uppercase character</li>
        <li>Number</li>
        <li>Special character</li>
      </ul>
    </article>
    <label>Password <span class="toggle-password" style="cursor:pointer"><i class="bi bi-eye-slash"></i></span>
      <input name="password" type="password" minlength="12"
        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{12,}$" required>
    </label>
    <label>Confirm Password <span class="toggle-password" style="cursor:pointer"><i class="bi bi-eye-slash"></i></span>
      <input name="confirmPass" type="password" minlength="12"
        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{12,}$" required>
    </label>
    <button type="submit"><i class="bi bi-person-plus"></i> Signup</button>
  </form>
  <p>Already have an account? <a href="/login">Login here</a>.</p>
</div>
<script type="module">
  import { configFormSubmit, togglePasswordVisibility } from '/assets/utils.js'
  configFormSubmit('#signupForm')
  togglePasswordVisibility()
</script>