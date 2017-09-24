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
$app->get('/test_translate', 'test_translate'); //test func mailer x invio mail

$app->post('/auto/translate', 'auto_translate'); //translate language
$app->post('/dg/setting/read', 'setting_read'); //read data app_setting.json
$app->post('/dg/setting/save', 'setting_save'); //save data app_setting.json
$app->post('/dg/crud/generate', 'crud_generate'); //generate code for crud
$app->get('/test_oci', 'test_oci'); //test driver oracle
$app->post('/dg/snippets/read', 'snippets_read'); //read file for snippets

include 'fn_api.php';
$start = new easyuigii();

$app->run();

/**
 * Test Api
 */
function test_api() {
    try {
        $app = Slim\Slim::getInstance();

        $app->render(200, ['success' => true, 'msg' => 'Hello']);
        error_log(LogTime() . 'test api' . PHP_EOL, 3, 'logs/api.log');
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . $e->getMessage() . PHP_EOL, 3, 'logs/error.log');
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

        $value = $app->request->params('value'); // Param keyword to translate


        $file = "../app_setting.json";
        $imp = file_get_contents($file);
        $ar_file = json_decode($imp, true);
        $lang2from = $ar_file["traduci dalla lingua"]; //translate from language
        $lang2to = $ar_file["traduci alla lingua"]; //translate to language

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
        error_log(LogTime() . $e->getMessage() . PHP_EOL, 3, 'logs/error.log');
    }
}

/** json setting read
 */
function setting_read() {
    try {
        $app = Slim\Slim::getInstance();

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
        error_log(LogTime() . $e->getMessage() . PHP_EOL, 3, 'logs/error.log');
    }
}

/** json setting save
 */
function setting_save() {
    try {
        $app = Slim\Slim::getInstance();


        $name = $app->request->params('name');
        $val = $app->request->params('val');


        $file = "../app_setting.json";
        $imp = file_get_contents($file);
        $ar_file = json_decode($imp, true);
        $ar_file[$name] = $val;
        $return = json_encode($ar_file);
        $return = str_replace(':"true"', ':true', $return);
        $return = str_replace(':"false"', ':false', $return);
        file_put_contents($file, $return);
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . $e->getMessage() . PHP_EOL, 3, 'logs/error.log');
    }
}

/**
 * code generator for crud
 */
function crud_generate() {
    try {
        $app = Slim\Slim::getInstance();

        $crud = new easyuigii;
        $crud->app_name = $app->request->params('app_name');
        $crud->app_folder = $app->request->params('app_folder');
        $crud->table_name = $app->request->params('table_name');

        $crud->build_app_crud();

        $app->render(200, ['success' => true, 'msg' => "eseguito"]);
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . $e->getMessage() . PHP_EOL, 3, 'logs/error.log');
    }
}

function test_oci() {
    try {
        $app = Slim\Slim::getInstance();

        $sql = "
                        SELECT  'ok' from dual
                ";

        $conn = oci_connect($db4_user, $db4_psw, $db4_GOLD, 'UTF8');
        $db = oci_parse($conn, $sql);
        $rs = oci_execute($db);
        oci_fetch_all($db, $data, null, null, OCI_ASSOC + OCI_FETCHSTATEMENT_BY_ROW);

        $app->response()->body(json_encode($data));
        if ($debug) {
            error_log(LogTime() . ' Sql test oci' . $sql . PHP_EOL, 3, 'logs/debug.log');
        }
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . ' Sql tets oci  ' . PHP_EOL . $sql . PHP_EOL, 3, 'logs/error.log');
    }
}

/** read list file snippets
 */
function snippets_read() {
    try {
        $app = Slim\Slim::getInstance();

        $list = new easyuigii();
        $data = $list->list_file_for_snippets();

        $app->response()->body(json_encode($data));
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - list file snippets  ' . PHP_EOL . $sql . PHP_EOL, 3, 'logs/error.log');
    }
}
