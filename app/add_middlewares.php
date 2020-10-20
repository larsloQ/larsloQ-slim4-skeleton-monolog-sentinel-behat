<?php 
declare(strict_types=1);


// add_middlewares.php
use App\Middleware\PhpErrorHandlerMiddleware;
use App\Middleware\UsageLoggerMiddleware;
use \App\Middleware\SentinelAuthCheckMiddleWare;


/* add APP-WIDE middlewares */

return function (Slim\App $app, Psr\Container\ContainerInterface $container, Array $settings) {

   
	/* auth middleware */
	// $basePath = $settings['general_settings']['basePath'];
	// $publicRoutes = $settings['general_settings']['public_routes'];
	// $app->add(new SentinelAuthCheckMiddleWare($container->get('sentinel'), $basePath, $publicRoutes));


	/**
	 * application level php-error-logger middleware
	 * config file returns a logger function
	 */
	if ($settings['log']['usage']['active']) {
	    $app->add(new UsageLoggerMiddleware($container->get('usage-logger')));
	}


	if ($settings['log']['php-error']['active']) {
	    $app->add(new PhpErrorHandlerMiddleware($container->get('php-error-logger')));
	}

	/* error output verbose on debug */
	if ($settings['general_settings']['debug']) {
    	$app->addErrorMiddleware(true, true, true); // show slim errors
	} 
	/**
	 * application level php-error-logger middleware
	 * config file returns a logger function
	 */
	if ($settings['log']['http-error']['active']) {
	    $app->add(new App\Middleware\HttpExceptionMiddleware($container->get('http-error-logger')));
	}
};