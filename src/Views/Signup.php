<form method="POST" action="/signup">
  <h2>Signup</h2>
  <article class="alert danger"><?= $error ?? '' ?></article>
  <label>Username
    <input name="username" type="text" autocapitalize="off" required>
  </label>
  <label>Password
    <input name="password" type="password" required>
  </label>
  <label>Confirm Password
    <input name="confirmPass" type="password" required>
  </label>
  <button type="submit">Signup</button>
</form>