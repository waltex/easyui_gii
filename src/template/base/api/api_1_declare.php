<?php

if (function_exists('xdebug_disable')) {
    xdebug_disable();
}

require '../vendor/autoload.php';

$app = new Slim\Slim();

//Add the middleware globally
$app->add(new \SlimJson\Middleware(array(
    'json.status' => false,
    'json.override_error' => true,
    'json.override_notfound' => true,
    'json.debug' => false,
    'json.cors' => true
)));

