<?php

require '../vendor/autoload.php';

use App\Middleware\Session;
use App\Controllers\Auth;
use App\Controllers\Dashboard;
use App\Controllers\Invoices;
use App\Controllers\Clients;

$app = new \GingerTek\Routy([
  'render' => \App\Utils::renderStrategy(...),
]);

try {
  $app->use(Session::init(...));
  $app->get('/login', Auth::viewLogin(...));
  $app->post('/login', Auth::postLogin(...));
  $app->get('/signup', Auth::viewSignup(...));
  $app->post('/signup', Auth::postSignup(...));
  $app->get('/logout', Session::id(...), Auth::getLogout(...));
  $app->group('/', Session::id(...), function () use ($app) {
    $app->get('/', fn() => $app->redirect('/dashboard'));
    $app->get('/dashboard', Dashboard::view(...));
    $app->group('/invoices', Invoices::routes(...));
    $app->group('/clients', Clients::routes(...));
  });
  $app->fallback(fn() => $app->render('NotFound'));
} catch (\Exception $ex) {
  $app->render('Error', ['error' => $ex->getMessage()]);
}
