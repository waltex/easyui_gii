<?php

SvuotaLog();

/**
 * automatic callback return for method JSONP
 * @param string $jsonp json data
 * @return string json callback function
 */
function jsonpWrap($jsonp) {
    include 'api_setup.php';
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
function SvuotaLog() {
    try {
        //$app = Slim\Slim::getInstance();
        include 'api_setup.php';
        $dir = "logs/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); //create folder
        }
        foreach ($param_log as $file => $value) {
            $file = $dir . $file;
            if ($value > -1) {
                if (file_exists($file)) {
                    if ((filesize($file) / 1024 / 1024) > $value) {
                        $path_info = pathinfo($file);
                        $file_old = $path_info['dirname'] . '/' . $path_info['filename'] . "_old." . $path_info['extension'];
                        copy($file, $file_old);
                        unlink($file);
                    }
                }
            };
        }

        /*
          if ($debug_log_mb > -1) {
          if (file_exists("debug.log")) {
          if ((filesize("debug.log") / 1024 / 1024) > $debug_log_mb) {
          copy("debug.log", "debug_old.log");
          unlink("debug.log");
          }
          }
          };
         */
    } catch (Exception $e) {
        //$app->stop($e);
    }
}
