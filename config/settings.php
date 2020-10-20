<?php
// phpcs:ignore error
return [
    'debug'            => true,
    'localpath'        => "/var/www/github/slim4",
    /* basePath need to be the same than in .htaccess RewriteBase
     * Up to Slim v3, Slim extracted the base path from the folder 
     * where the application was instantiated. 
     * This is no longer the case, and the base path must be explicitly 
     * declared in case your application is not executed from the root of your domain
     * (no trailing slash !!! )
     */
    // 'basePath' => "", // when domain/subdomain is pointing to the public folder!
    'basePath'         => "/github/slim4/public",
    'data_file'        => 'all.json',
    // make sure that webserver has READER and WRITE access !
    'data_repo_folder' => '/var/www/github/slim4/data',
    'data_folder'      => '/var/www/github/slim4/data/live',
];
