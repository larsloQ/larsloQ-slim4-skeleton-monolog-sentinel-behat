<?php
/* make sure that folders exist and are writeable for your webserver
mkdir log
mkdir log/php-error
mkdir log/http-error
mkdir log/usage
sudo chown larslo:www-data log/ -R
sudo chmod g+w log/* -R
sudo chmod u+w log/* -R
 */
return [
    "php-error"  => [
        'active'   => true,
        'timezone' => "UTC",
        'path'     => '../log/php-error/php-error.log',
        'name'     => 'PHP-Error',
    ],
    "http-error" => [
        'active'   => true,
        'timezone' => "UTC",
        'path'     => '../log/http-error/http-error.log',
        'name'     => 'HTTP-Error',
    ],
    "usage"      => [
        'active'   => true,
        'timezone' => "UTC",
        'path'     => '../log/usage/usage.log',
        'name'     => 'App-Usage',
    ],
];
