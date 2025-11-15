<!DOCTYPE HTML>
<html>
<head>
  <title><?= $title ?? 'Home' ?> - Booky</title>
  <link rel="stylesheet" href="//unpkg.com/@picocss/pico/css/pico.min.css">
  <link rel="stylesheet" href="//unpkg.com/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/styles.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body class="container">
  <header>
    <nav>
      <ul>
        <li><b>Booky</b></li>
      </ul>
      <ul>
        <li>
          <details class="dropdown">
            <summary>
              <?php if ($isAuthed): ?>
              <i class="bi bi-person-circle"></i> <?= $user->username ?>
              <?php else: ?>
              <i class="bi bi-list"></i>
              <?php endif ?>
            </summary>
            <ul dir="rtl">
              <?php if ($isAuthed): ?>
              <li dir="ltr"><a href="/dashboard">Dashboard</a></li>
              <li dir="ltr"><a href="/invoices">Invoices</a></li>
              <li><hr></li>
              <li dir="ltr"><a href="/logout">Logout</a></li>
              <?php else: ?>
              <li dir="ltr"><a href="/login">Login</a></li>
              <li dir="ltr"><a href="/signup">Signup</a></li>
              <?php endif ?>
            </ul>
          </details>
        </li>
      </ul>
    </nav>
  </header>
  <main>
    <?php include $view ?>
  </main>
  <script type="module">
    import { submitBusy } from '/assets/utils.js'
    submitBusy()
  </script>
</body>
</html>
