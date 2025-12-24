<?php

define('ROOT', __DIR__ . '/../');
require_once 'vendor/autoload.php';
try {
  \App\Services\Config::load();
  $action = $argv[1] ?? null;
  if ($action === 'init-db') {
    $driver = getenv('DB_DRIVER') ?: 'sqlite';
    $schema = "src/Data/schema_$driver.sql";
    if (!file_exists($schema))
      throw new Exception("No $driver schema file found");
    (new \App\Data\Database)->exec(file_get_contents($schema));
  } elseif ($action === 'users') {
    $subAction = $argv[2] ?? null;
    if ($subAction === 'list') {
      $flag = $argv[3] ?? null;
      $enabled = $flag === 'enabled' ? true : ($flag === 'disabled' ? false : null);
      $users = (new \App\Services\Users)->list(['enabled' => $enabled]);
      if (count($users) > 0) {
        $out = [
          'ID            | Username       | Email                | Enabled | Created          | Updated         ',
          '-----------------------------------------------------------------------------------------------------'
        ];
        foreach ($users as $user)
          $out[] = join(' | ', [
            $user->id,
            str_pad(substr($user->username, 0, 14), 14, ' ', STR_PAD_RIGHT),
            str_pad(substr($user->email, 0, 20), 20, ' ', STR_PAD_RIGHT),
            str_pad(substr($user->enabled == 1 ? 'Yes' : 'No', 0, 7), 7, ' ', STR_PAD_RIGHT),
            str_pad(substr($user->created, 0, 16), 16, ' ', STR_PAD_RIGHT),
            str_pad(substr($user->updated, 0, 16), 16, ' ', STR_PAD_RIGHT)
          ]);
        echo join("\n", $out) . "\n";
      } else
        echo "No users found\n";
    } elseif ($subAction === 'add' && isset($argv[3], $argv[4], $argv[5])) {
      $username = $argv[3] ?? null;
      $email = $argv[4] ?? null;
      $passhash = password_hash($argv[5], PASSWORD_DEFAULT);
      $user = (new \App\Services\Users)->create($username, $email, $passhash);
      echo "User created with ID $user->id\n";
    } elseif ($subAction === 'enable' && isset($argv[3])) {
      $userId = $argv[3] ?? null;
      if ((new \App\Services\Users)->enable($userId))
        echo "User $userId enabled\n";
      else
        echo "User $userId not found\n";
    } elseif ($subAction === 'disable' && isset($argv[3])) {
      $userId = $argv[3] ?? null;
      if ((new \App\Services\Users)->disable($userId))
        echo "User $userId disabled\n";
      else
        echo "User $userId not found\n";
    } else {
      echo "Unknown users action\n";
    }
  } else {
    echo "actions: init-db, users [list [enabled|disabled], enable <id>, disable <id>]\n";
  }
} catch (Exception $e) {
  echo "Error: " . $e->getMessage() . "\n";
}
