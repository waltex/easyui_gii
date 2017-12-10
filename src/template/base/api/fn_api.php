<?php

SvuotaLog();

function gii_add_col_combo($ar_combo, $ar_dg, $field_dg, $value_field, $text_field) {
    $ar_dg2 = [];
    foreach ($ar_dg as $value) {
        $find = $value[$field_dg];
        $key = gii_array_search_multi($find, $value_field, $ar_combo);
        if ($key >= 0) {
            $text = $ar_combo[$key][$text_field];
            $value[$field_dg . "__TEXT"] = $text;
            array_push($ar_dg2, $value);
        } else {
            // not find
            $value[$field_dg . "__TEXT"] = null;
            array_push($ar_dg2, $value);
        }
    }
    return $ar_dg2;
}

function gii_array_search_multi($value, $key, $array) {
    foreach ($array as $k => $val) {
        if ($val[$key] == $value) {
            return $k;
        }
    }
    return -1;
}

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
    return date("D M d, Y G:i:s", time());
}

/** Svuota i log in base alle impostazioni
 *
 */
function SvuotaLog() {
    try {
        //$app = Slim\Slim::getInstance();
        include 'api_setup.php';

        if ($debug_log_mb > -1) {
            if (file_exists("debug.log")) {
                if ((filesize("debug.log") / 1024 / 1024) > $debug_log_mb) {
                    copy("debug.log", "debug_old.log");
                    unlink("debug.log");
                }
            }
        };

        if ($api_log_mb > -1) {
            if (file_exists("api.log")) {
                if ((filesize("api.log") / 1024 / 1024) > $api_log_mb) {
                    copy("api.log", "api_old.log");
                    unlink("api.log");
                }
            }
        };
        if ($error_log_mb > -1) {
            if (file_exists("error.log")) {

                if ((filesize("error.log") / 1024 / 1024) > $error_log_mb) {
                    copy("error.log", "error_old.log");
                    unlink("error.log");
                }
            }
        };
    } catch (Exception $e) {
        //$app->stop($e);
    }
}
