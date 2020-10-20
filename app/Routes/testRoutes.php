<?php
namespace App\Routes;

/* 
 * register a route with raises both (a http-error and a PHP error)
 * used for testing error logger
 */
if ($settings['general_settings']['debug']) {

    $app->get('/error', function ($request, $response, $args) {
        $a = [];
        /* will raise a notice */
        $b = $a['asdsds'];
        try {
            $error = 'Always throw this error';
            throw new \Exception($error);
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        
        $response = $response->withStatus(400);
        $response->getBody()->write("raised error");
        return $response;
    });
}
