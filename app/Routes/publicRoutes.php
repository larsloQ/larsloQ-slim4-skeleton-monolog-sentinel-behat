<?php
namespace App\Routes;

/*
* receive texts for website
 */
$app->get('/texts', function ($request, $response, $args) {
    $settings   = $this->get('settings')['general_settings'];
    $dataFolder = $settings['data_folder'];
    $fileName   = $settings['data_file'];
    $file       = $dataFolder . "/" . $fileName.".json";
    $content    = file_get_contents($file);
    $response->getBody()->write(json_encode($content, JSON_UNESCAPED_UNICODE));
    return $response;
});

/* post to auth to receive auth token / cookie */
$app->post('/auth', function ($request, $response, $args) {
    $body        = $request->getParsedBody();
    $sentinel    = $this->get("sentinel");
    $credentials = [
        'email'    => $body['username'],
        'password' => $body['password'],
    ];
    $user = $sentinel->authenticateAndRemember($credentials);
    if (!$user) {
        $response = $response->withStatus(403);
        $body     = $response->getBody()->write("Username / Password falsch");
        return $response;
    }
    $response->getBody()->write(json_encode($user));
    return $response;

});

/* just a non-sense route mostly for testing reasons*/
$app->get('/', function ($request, $response, $args) {
    $body = $response->getBody()->write(json_encode(["say" => "hey"]));
    return $response;
});

$app->get('/hi', function ($request, $response, $args) {
    $body = $response->getBody()->write(json_encode(["say" => "hey"]));
    return $response;
});
