<?php

require '../vendor/autoload.php';

use App\Middleware\Session;
use App\Controllers\Auth;
use App\Controllers\Dashboard;
use App\Controllers\Invoices;

$app = new \GingerTek\Routy([
  'render' => function($view, $ctx, $app) {
    ob_start();
    $user = $app->getCtx('user');
    extract([
      ...$ctx,
      'isAuthed' => !!$user,
      'user' => $user
    ], EXTR_OVERWRITE);
    $view = "../src/Views/$view.php";
    include "../src/Views/_Layout.php";
    exit(ob_get_clean());
  }
]);

try {
  $app->use(Session::init(...));
  $app->get('/login', Auth::viewLogin(...));
  $app->post('/login', Auth::postLogin(...));
  $app->get('/signup', Auth::viewSignup(...));
  $app->post('/signup', Auth::postSignup(...));
  $app->get('/logout', Session::id(...), Auth::getLogout(...));
  $app->group('/', Session::id(...), function () use($app) {
    $app->get('/dashboard', Dashboard::view(...));
    $app->group('/invoices', Invoices::routes(...));
  });
  $app->fallback(fn() => $app->render('NotFound'));
} catch(\Exception $ex) {
  $app->render('Error', ['error' => $ex->getMessage()]);
}
