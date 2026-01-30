<!DOCTYPE HTML>
<html>

<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'Home' ?> - Booky</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="//unpkg.com/@picocss/pico/css/pico.indigo.min.css">
  <link rel="stylesheet" href="//unpkg.com/bootstrap-icons/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/styles.css">
</head>

<body class="container">
  <header>
    <nav>
      <ul>
        <li style="font-size:1.5em;font-weight:bold"><a href="/">Booky</a></li>
      </ul>
      <ul>
        <li>
          <details class="dropdown">
            <summary>
              <?php if ($isAuthed): ?>
                <i class="bi bi-person-circle"></i> <?= $user->username ?>
              <?php else: ?>
                <i class="bi bi-list"></i> Menu
              <?php endif ?>
            </summary>
            <ul dir="rtl">
              <?php if ($isAuthed): ?>
                <li dir="ltr"><a href="/dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li dir="ltr"><a href="/invoices"><i class="bi bi-receipt"></i> Invoices</a></li>
                <li dir="ltr"><a href="/clients"><i class="bi bi-people"></i> Clients</a></li>
                <li dir="ltr"><a href="/expenses"><i class="bi bi-journal-text"></i> Expenses</a></li>
                <li dir="ltr"><a href="/templates"><i class="bi bi-file-earmark-text"></i> Templates</a></li>
                <li dir="ltr"><a href="/settings"><i class="bi bi-gear"></i> Settings</a></li>
                <li>
                  <hr>
                </li>
                <li dir="ltr"><a href="/logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
              <?php else: ?>
                <li dir="ltr"><a href="/login"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
                <li dir="ltr"><a href="/signup"><i class="bi bi-person-plus"></i> Signup</a></li>
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
  <?php if ($isAuthed): ?>
    <script type="module">
      import { startSessionTimer, themeSwitcher } from '/assets/utils.js'
      startSessionTimer()
    </script>
  <?php endif ?>
</body>

</html>