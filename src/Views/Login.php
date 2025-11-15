<form method="POST" action="/login">
  <h2>Login</h2>
  <article class="alert danger"><?= $error ?? '' ?></article>
  <label>Username
    <input name="username" type="text" autocapitalize="off" required>
  </label>
  <label>Password
    <input name="password" type="password" required>
  </label>
  <button type="submit">Login</button>
</form>
