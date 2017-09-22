<?php

/**
 * automatic callback return for method JSONP
 * @param string $jsonp json data
 * @return string json callback function
 */
function jsonpWrap($jsonp) {
    //include 'api_setup.php';
    $app = Slim\Slim::getInstance();
    if (($jsonCallback = $app->request()->get('callback')) !== null) {
        $jsonp = sprintf("%s(%s);", $jsonCallback, $jsonp);
        $app->response()->header('Content-type', 'application/javascript');
        if ($debug == true)
            error_log(LogTime() . 'callback return: ' . PHP_EOL . $jsonp . PHP_EOL, 3, 'debug.log');
    } else
//if ($debug == true)
//    error_log(LogTime() . 'no_callback return: ' . $jsonp . PHP_EOL, 3, 'debug.log');
        return $jsonp;
}

//Log
function logTime() {
    return date("D M d, Y G:i:s ", time());
}

function message_err($e) {
    return basename($e->getFile()) . ":" . $e->getLine() . " - " . $e->getMessage() . PHP_EOL;
}

/** Svuota i log in base alle impostazioni
 *
 */

