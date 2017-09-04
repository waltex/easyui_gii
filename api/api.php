<?php

if (function_exists('xdebug_disable')) {
    //xdebug_disable();
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

$app->get('/test_api', 'test_api'); // test api
$app->get('/test_mailer', 'test_mailer'); //test func mailer x invio mail
$app->get('/test_translate', 'test_translate'); //test func mailer x invio mail

$app->post('/auto/translate', 'auto_translate'); //translate language
$app->post('/dg/setting/read', 'setting_read'); //read data app_setting.json

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

/**
 * Test Api
 */
function test_api() {
    try {
        $app = Slim\Slim::getInstance();
        include 'api_setup.php';

        $app->render(200, ['success' => true, 'msg' => 'Hello']);
        error_log(LogTime() . 'test api' . PHP_EOL, 3, 'api.log');
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . $e->getMessage() . PHP_EOL, 3, 'error.log');
    }
}

function test_mailer() {
    mail2("waltex@libero.it", "oggetto", "<h1>corpo</h1>", ["files_" => ["TEST_API.txt", "api.log"], "isHTML_" => true, "setFrom_" => "walter.cardelli@labbondanza.it"]);
    //mail2("walter.cardelli@labbondanza.it", "oggetto", "<h1>corpo</h1>", ["files_" => ["TEST_API.txt", "api.log"], "isHTML_" => true, "setFrom_" => "walter.cardelli@labbondanza.it"]);

    /*
      mail2("waltex <walter.cardelli@labbondanza.it>", "oggetto", "<h1>corpo</h1>"
      // valori optionali
      , [
      "files_" => ["TEST_API.txt", "api.log"]
      , "isHTML_" => true //false default
      , "setFrom" => "CED <ced@labbondanza.it>" // solo una mail
      , "addCC" => "Waltex Libero <waltex@libero.it>,Waltex  <waltex79@libero.it>"
      , "addReplyTo" => "Waltex Libero <waltex@libero.it>,Waltex  <waltex79@libero.it>"
      , "addBCC" => "Waltex Libero <waltex@libero.it>,Waltex  <waltex79@libero.it>"
      ]);
     */
    /*
      $file_local = "TMP/ddt_bancoalim.xls";
      mail2("walter.cardelli@labbondanza.it"
      , "test allegato mail"
      , "Mail automatica" . PHP_EOL . "Vedi allegato."
      , ["files" => [$file_local]]);
     */
}

/**
 *
 * @param type $mail
 * @param type $tipo 0 mail, 1 nominativo 'Fname Lname<fname@urmail.com>'
 */
function mailer_get_mail($mail, $tipo) {
    //testo mail
    if ($tipo == 1) {
        $input = $mail; // 'Fname Lname<fname@urmail.com>';
        preg_match('~<(.*?)>~', $input, $output);

        if (Count($output) > 0) {
            $email_val = $output[1];
            $testo_email = str_replace($email_val, "", $mail);
            $testo_email = str_replace("<>", "", $testo_email);

            return $testo_email;
            //$mail->addAddress($email, $testo_email); // 'Fname Lname<fname@urmail.com>';
        } else {
            return null;
        }
    }

    //mail
    if ($tipo == 0) {
        $input = $mail; // 'Fname Lname<fname@urmail.com>';
        preg_match('~<(.*?)>~', $input, $output);

        if (Count($output) > 0) {
            return $output[1]; //estrae mail
        } else {
            return $mail; //non estrae mail perche non cè bisogno
        }
    }
}

/**
 *
 * @param type $mail_to
 * @param type $oggetto
 * @param type $testo
 * @param type $opt
 *
 * es.   mail2("waltex <walter.cardelli@labbondanza.it>", "oggetto", "<h1>corpo</h1>"
  // valori optionali
  , [
  "files_" => ["TEST_API.txt", "api.log"]
  , "isHTML_" => true //false default
  , "setFrom" => "CED <ced@labbondanza.it>" // solo una mail
  , "addCC" => "Waltex Libero <waltex@libero.it>,Waltex  <waltex79@libero.it>"
  , "addReplyTo" => "Waltex Libero <waltex@libero.it>,Waltex  <waltex79@libero.it>"
  , "addBCC" => "Waltex Libero <waltex@libero.it>,Waltex  <waltex79@libero.it>"
  ]);
 *
 */
function mail2($mail_to, $oggetto, $testo, $opt = null) {

    //mail("mail_to", "oggetto", "testo", "opt"); //old mail

    include_once '../lib/PHPMailer/PHPMailerAutoload.php';
    include 'api_setup.php';

    $mail = new PHPMailer;
    $mail->SMTPDebug = $mailer_SMTPDebug;
    //$mail->SMTPDebug = 3;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = $mailer_Host; //smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = $mailer_SMTPAuth;                 // Enable SMTP authentication
    $mail->Username = $mailer_Username;                 // SMTP username
    $mail->Password = $mailer_Password;                 // SMTP password
    $mail->SMTPSecure = $mailer_SMTPSecure; //'tls';    // Enable TLS encryption, `ssl` also accepted
    $mail->Port = $mailer_Port;                         // TCP port to connect to
    //optionale mittente, default $mailer_from="ced@labbondanza.it (vedi api_setup.php)
    // isHTML=>true
    if ($opt != null) {
        if (isset($opt["setFrom"])) {
            $mail_val = mailer_get_mail($opt["setFrom"], 0); //inirizzo mail
            $mail_testo = mailer_get_mail($opt["setFrom"], 1); //testo amil
            $mail->setFrom($mail_val, $mail_testo);
        } else {
            $mail_val = mailer_get_mail($mailer_from, 0); //inirizzo mail
            $mail_testo = mailer_get_mail($mailer_from, 1); //testo amil
            $mail->setFrom($mail_val, $mail_testo);
        }
    } else {
        //default
        $mail_val = mailer_get_mail($mailer_from, 0); //inirizzo mail
        $mail_testo = mailer_get_mail($mailer_from, 1); //testo amil
        $mail->setFrom($mail_val, $mail_testo);
    }



    //$mail->addAddress('walter.cardelli@labbondanza.it', 'Joe User');     // Add a recipient
    $mails_to = explode(",", $mail_to);
    foreach ($mails_to as $value) {
        $mail_val = mailer_get_mail($value, 0); //inirizzo mail
        $mail_testo = mailer_get_mail($value, 1); //testo amil
        $mail->addAddress($mail_val, $mail_testo);
    }

    //$mail->addAddress($mail_to);               // Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');
    //
    //
    //CC eamil ("addCC"=>"pippo@dominio.it")
    if ($opt != null) {
        if (isset($opt["addCC"])) {
            $mails = explode(",", $opt["addCC"]);
            foreach ($mails as $value) {
                $mail_val = mailer_get_mail($value, 0); //inirizzo mail
                $mail_testo = mailer_get_mail($value, 1); //testo amil
                $mail->addCC($mail_val, $mail_testo);
            }
        }
    }

    //ReplyTo eamil ("ReplyTo"=>"pippo@dominio.it")
    if ($opt != null) {
        if (isset($opt["addReplyTo"])) {
            $mails = explode(",", $opt["addReplyTo"]);
            foreach ($mails as $value) {
                $mail_val = mailer_get_mail($value, 0); //inirizzo mail
                $mail_testo = mailer_get_mail($value, 1); //testo amil
                $mail->addReplyTo($mail_val, $mail_testo);
            }
        }
    }

    //BCC eamil ("addBCC"=>"pippo@dominio.it")
    if ($opt != null) {
        if (isset($opt["addBCC"])) {
            $mails = explode(",", $opt["addBCC"]);
            foreach ($mails as $value) {
                $mail_val = mailer_get_mail($value, 0); //inirizzo mail
                $mail_testo = mailer_get_mail($value, 1); //testo amil
                $mail->addBCC($mail_val, $mail_testo);
            }
        }
    }






    if ($opt != null) {
        if (isset($opt["files"])) {
            foreach ($opt["files"] as $value) {
                $mail->addAttachment($value);
            }
        }
    }

    //optionale mail in formato html, default no
    // isHTML=>true
    if ($opt != null) {
        (isset($opt["isHTML"])) ? $mail->isHTML($opt["isHTML"]) : $mail->isHTML(false);
    } else {
        $mail->isHTML(false); //default
    }



    $mail->Subject = $oggetto;
    $mail->Body = ($testo == "") ? " " : $testo;
    ; // 'This is the HTML message body <b>in bold!</b>';
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    if (!$mail->send()) {
        //echo 'Message could not be sent.';
        //echo 'Mailer Error: ' . $mail->ErrorInfo;
        error_log(LogTime() . ' errore invio mail: ' . $mail->ErrorInfo . PHP_EOL, 3, 'error.log');
    } else {
        //echo 'Message has been sent';
    }
}

function test_translate() {
    $tr = new \Stichoza\GoogleTranslate\TranslateClient(); // Default is from 'auto' to 'en'
    $tr->setSource('en'); // Translate from English
    $tr->setTarget('it'); // Translate to Georgian
    echo $tr->translate('Hello World!');
}

function auto_translate() {
    try {
        $app = Slim\Slim::getInstance();
        include 'api_setup.php';

        $value = $app->request->params('value'); // Param keyword to translate


        $file = "../app_setting.json";
        $imp = file_get_contents($file);
        $ar_file = json_decode($imp, true);
        $lang2from = $ar_file["language from translate"];
        $lang2to = $ar_file["language to translate"];

        /*
          //ex "stichoza/google-translate-php": "~3.2",
          $tr = new \Stichoza\GoogleTranslate\TranslateClient(); // Default is from 'auto' to 'en'
          $tr->setSource($lang2from); // Translate from English
          $tr->setTarget($lang2to); // Translate to Georgian
          $value_t = $tr->translate($value); //value trnslated
         */


        $trans = new \Statickidz\GoogleTranslate();
        $value_t = $trans->translate($lang2from, $lang2to, $value);



        $file = "../language/$lang2from" . "2" . "$lang2to.json";
        if (!file_exists($file)) {
            file_put_contents($file, json_encode([]));
        }
        $imp = file_get_contents($file);
        $ar_file = json_decode($imp, true);
        $ar_out = array_merge($ar_file, [$value => $value_t]);
        file_put_contents($file, json_encode($ar_out));

        $app->render(200, [$value => $value_t]);
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . $e->getMessage() . PHP_EOL, 3, 'error.log');
    }
}

function setting_read() {
    try {
        $app = Slim\Slim::getInstance();
        include 'api_setup.php';

        $file = "../app_setting.json";
        $imp = file_get_contents($file);
        $ar_file = json_decode($imp, true);
        $ar_out = [];
        foreach ($ar_file as $key => $value) {
            $ar_out[] = ["name" => $key, "val" => $value];
        }

        $app->render(200, $ar_out);
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . $e->getMessage() . PHP_EOL, 3, 'error.log');
    }
}
