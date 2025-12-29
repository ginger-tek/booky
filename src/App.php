<?php

require '../vendor/autoload.php';

use App\Middleware\Session;
use App\Controllers\Auth;
use App\Controllers\Dashboard;
use App\Controllers\Invoices;
use App\Controllers\Clients;
use App\Controllers\Settings;

\App\Services\Config::load();

$app = new \GingerTek\Routy([
  'render' => \App\Utils::renderStrategy(...),
]);

try {
  $app->use(Session::init(...));
  $app->get('/login', Auth::viewLogin(...));
  $app->post('/login', Auth::postLogin(...));
  $app->get('/signup', Auth::viewSignup(...));
  $app->post('/signup', Auth::postSignup(...));
  $app->get('/signup-success', Auth::viewSignupSuccess(...));
  $app->group('/', Session::id(...), function () use ($app) {
    $app->post('/refresh', Auth::postExtendSession(...));
    $app->get('/logout', Auth::getLogout(...));
    $app->get('/', fn() => $app->redirect('/dashboard'));
    $app->get('/dashboard', Dashboard::view(...));
    $app->group('/invoices', Invoices::routes(...));
    $app->group('/clients', Clients::routes(...));
    $app->get('/expenses', Dashboard::viewExpenses(...));
    $app->group('/settings', Settings::routes(...));
  });
  $app->fallback(fn() => $app->render('NotFound', ['title' => 'Page Not Found']));
} catch (\Exception $ex) {
  $app->render('Error', ['error' => $ex->__toString(), 'title' => 'Uh Oh! An Error Occurred']);
}
