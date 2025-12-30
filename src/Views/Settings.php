<article>
  <form method="post" action="/settings/update-user">
    <div class="flex spread bottom-spacing">
      <h2>User Settings</h2>
      <button type="submit" style="width:auto"><i class="bi bi-floppy"></i> Save</button>
    </div>
    <article class="alert info">Username cannot be changed</article>
    <label>Username
      <input type="text" value="<?= $user->username ?>" disabled readonly>
    </label>
    <label>Email
      <input type="email" name="email" value="<?= $user->email ?>" required>
    </label>
  </form>
</article>
<article>
  <form method="post" action="/settings/update-password">
    <h2>Change Password</h2>
    <article class="alert info">Password must be 12 or more characters long, and include at least one of each of the
      following:
      <ul style="color:inherit" class="bottom-clear">
        <li>Lowercase character</li>
        <li>Uppercase character</li>
        <li>Number</li>
        <li>Special character</li>
      </ul>
    </article>
    <label>Current Password <span class="toggle-password" style="cursor:pointer"><i class="bi bi-eye-slash"></i></span>
      <input name="currentPassword" type="password" minlength="12"
        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{12,}$" required>
    </label>
    <label>New Password <span class="toggle-password" style="cursor:pointer"><i class="bi bi-eye-slash"></i></span>
      <input name="newPassword" type="password" minlength="12"
        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{12,}$" required>
    </label>
    <label>Confirm New Password <span class="toggle-password" style="cursor:pointer"><i
          class="bi bi-eye-slash"></i></span>
      <input name="confirmNewPass" type="password" minlength="12"
        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{12,}$" required>
    </label>
    <button type="submit">Change Password</button>
  </form>
</article>
<script type="module">
  import { ctrlSave, togglePasswordVisibility } from '/assets/utils.js'
  ctrlSave(document.querySelector('form'))
  togglePasswordVisibility()
</script>