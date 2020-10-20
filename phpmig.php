<?php
// date_default_timezone_set("Asia/Jakarta");
require_once 'vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;
use Phpmig\Adapter;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$folder = __DIR__ . '/' . $_ENV['CONFIG_FOLDER'];
$db        = require $folder . '/database.php';
$capsule = new Capsule;
$capsule->addConnection($db);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container                                    = new ArrayObject();
$container['phpmig.adapter']                  = new Adapter\Illuminate\Database($capsule, 'migrations');
$container['phpmig.migrations_path']          = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';
$container['phpmig.migrations_template_path'] = $container['phpmig.migrations_path'] . DIRECTORY_SEPARATOR . '.template.php';
return $container;
