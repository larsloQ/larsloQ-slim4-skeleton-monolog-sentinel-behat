<?php

declare (strict_types = 1);

use Slim\Factory\AppFactory;



require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/setup_container.php';
require __DIR__ . '/add_middlewares.php';

$container = get_container();
$settings  = get_settings();




/*
Set container to create App with on AppFactory
 */
AppFactory::setContainer($container);
$app = AppFactory::create();

/*
basePath needs to be the same than in .htaccess RewriteBase
Up to v3, Slim extracted the base path from the folder where the application was instantiated. This is no longer the case, and the base path must be explicitly declared in case your application is not executed from the root of your domain:
 */

$basePath = $settings['general_settings']['basePath'];
$app->setBasePath($basePath);

/* slim basic middlewares */
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

/*Register routes*/
require __DIR__ . '/Routes/privateRoutes.php';
require __DIR__ . '/Routes/publicRoutes.php';
require __DIR__ . '/Routes/testRoutes.php';

/* add custom middlewares */
$mw = include __DIR__ . '/add_middlewares.php';
$mw($app, $container, $settings);

$app->run();
