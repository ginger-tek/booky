<?php

namespace App\Services;

class Config
{
  public static function load(string $path = '.env')
  {
    $path = ROOT . $path;
    if (file_exists($path))
      foreach (parse_ini_file($path) as $k => $v)
        putenv(trim("$k=$v"));
  }
}
