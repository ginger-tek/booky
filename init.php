<?php

define('ROOT', __DIR__ . '/../');
require_once 'vendor/autoload.php';
\App\Services\Config::load();
$driver = getenv('DB_DRIVER') ?: 'sqlite';
$schema = "src/Data/schema_$driver.sql";
if (file_exists($schema)) (new \App\Data\Database)->exec(file_get_contents($schema));
else throw new Exception("No $driver schema file found");
