<?php
// setup_container.php

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use DI\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
// use Monolog\Logger as Logger;

function get_settings()
{
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
    $dotenv->load();
    $folder = __DIR__ . '/../' . $_ENV['CONFIG_FOLDER'];

    $settings                     = [];
    $settings['general_settings'] = require $folder . '/settings.php';
    $settings['database']         = require $folder . '/database.php';
    $settings['log']              = require $folder . '/log.php';
    return $settings;
}

function get_container()
{
    $settings = get_settings();
// Create Container using PHP-DI
    $container = new Container();

/* add settings to container */
    $container->set('settings', function () use ($settings) {
        return $settings;
    });

/* monologer service  */
    $container->set("php-error-logger", function () use ($settings) {
        if ($settings['log']['php-error']['active']) {
            $logger = new App\Services\Logger($settings['log']['php-error']);
            return $logger->getLogger();
        }
    });
    $container->set("http-error-logger", function () use ($settings) {
        if ($settings['log']['http-error']['active']) {
            $logger = new App\Services\Logger($settings['log']['http-error']);
            return $logger->getLogger();
        }
    });
    $container->set("usage-logger", function () use ($settings) {
        if ($settings['log']['usage']['active']) {
            $logger = new App\Services\Logger($settings['log']['usage']);
            return $logger->getLogger();
        }
    });

/* add sentinel user auth lib to container */
    $container->set('sentinel', function () use ($settings) {
        $sentinel = (new Sentinel());
        $capsule  = new Capsule;
        $capsule->addConnection($settings['database']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $debug = $settings['general_settings']['debug'];
        if ($debug == true) {
            $capsule::connection()->enableQueryLog();
        }
        return $sentinel->getSentinel();
    });

    return $container;
}
