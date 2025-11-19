<?php

namespace App;

class Utils
{
  public static function slashDate(string|null $date = ''): string
  {
    if (!$date) return '';
    return (new \DateTime($date))->format('n/j/Y');
  }

  public static function currency(int|float|null $amount = 0): string
  {
    $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
    return $formatter->format($amount);
  }

  public static function renderStrategy(string $view, array $ctx, \GingerTek\Routy $app): void
  {
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

  public static function reqSet(string $key, mixed $value): void
  {
    $_REQUEST[$key] = $value;
  }

  public static function reqGet(string $key): mixed
  {
    return $_REQUEST[$key] ?? null;
  }
}