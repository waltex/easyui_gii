<?php

$app->run();


SvuotaLog();

//Log
function logTime() {
    include 'api_setup.php';
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $user = "";
    if (isset($_SESSION['user' . "_$APP_NAME"])) {
        $user = $_SESSION['user' . "_$APP_NAME"];
        //$user = str_replace("$APP_NAME_", "", $user);
    }
    // close the session
    session_write_close();
    return date("D M d, Y G:i:s", time()) . " (" . $user . ") ";
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
