<?php

if (function_exists('xdebug_disable')) {
    //xdebug_disable();
}

require '../vendor/autoload.php';

$app = new Slim\Slim(array(
        //log.enabled' => true,
        //'log.writer' => new easyuigii()
        ));

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
$app->post('/dg/snippets/add', 'snippets_add'); //add file for snippets
$app->post('/dg/snippets/delete', 'snippets_delete'); //deleet snippets
$app->post('/dg/snippets/rename', 'snippets_rename'); //rename snippets
$app->post('/uoload/image', 'upload_image'); //upload image snippets
$app->post('/delete/uoload/image', 'del_upload_image'); //delete uploadet image snippets
$app->post('/dg/model/read/db/:table', 'dg_model_read_from_db'); //read model from db
$app->post('/list/table/db', 'list_table_db'); //for combobox, list table db
$app->post('/crud/save/cfg2json', 'save_cfg2json'); //save configuration to json
$app->post('/list/all/cfg/:folder', 'list_cfg'); //list name all configuration saved for project(as folder)
$app->post('/crud/open/cfg/json', 'open_cfg_from_json'); //save configuration to json
$app->post('/set/width/field/form', 'set_width_form'); //set default width form
$app->post('/list/project', 'list_project'); //set default width form
$app->post('/get/sql/crud', 'get_sql_crud'); //get sql for crud
$app->post('/get/sql/combo', 'get_sql_combo'); //get sql for combo



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
        $cat = "generica";
        $cat_old = "generica";
        foreach ($ar_file as $key => $value) {
            if ($value === "#cat") {
                $cat = $key;
            } else {
                $ar_out[] = ["name" => $key, "val" => $value, "cat" => $cat];
            }
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

        $app->render(200, ['success' => true]);
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

        $gii = new easyuigii;
        $gii->app_name = $app->request->params('app_name');
        $gii->app_folder = $app->request->params('app_folder');
        $gii->table_name = $app->request->params('table_name');
        $gii->model_from_json = $app->request->params('model_from_json');
        $gii->table_model = $app->request->params('model');

        $gii->html_prefix = $app->request->params('html_prefix');
        $gii->pagination = $app->request->params('pagination');
        $gii->pagination_list = $app->request->params('pagination_list');
        $gii->pagination_size = $app->request->params('pagination_size');
        $gii->dg_inline = $app->request->params('dg_inline');
        $gii->width_form = $app->request->params('width_form');
        $gii->height_form = $app->request->params('height_form');
        $gii->filter_base = $app->request->params('filter_base');
        $gii->ck_custom_sql = $app->request->params('ck_custom_sql');
        $gii->custom_sql = $app->request->params('custom_sql');
        $gii->ck_global_var = $app->request->params('ck_global_var');
        $gii->global_var = $app->request->params('global_var');
        $gii->ck_row_styler = $app->request->params('ck_row_styler');
        $gii->row_styler = $app->request->params('row_styler');

        $gii->build_app_crud();

        $app->render(200, ['success' => true, 'msg' => "eseguito"]);

        ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
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

        $filter = $app->request->params('filter'); // name snippets
        $ext = $app->request->params('name'); // name snippets
        $filter_content = $app->request->params('filter_content');

        $page = $app->request->params('page'); // Param from pagination
        $rows = $app->request->params('rows'); // Param from pagination

        if ($page <= 1) {
            $da = 1;
            $a = $rows;
        } else {
            $da = ($page - 1) * $rows;
            $da = $da + 1;
            $a = $page * $rows;
        }

        $gii = new easyuigii();
        $data = $gii->list_file_for_snippets($filter, $ext, $filter_content);

        //for pagination
        $ar = [];
        $riga = 1;
        foreach ($data as $value) {
            $riga += 1;
            if (($riga >= $da) && ($riga <= $a)) {
                $ar[] = $value;
            }
        }
        $result['rows'] = $ar;
        $result['total'] = Count($data);


        $app->response()->body(json_encode($result));

        ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - list file snippets  ' . PHP_EOL, 3, 'logs/error.log');
    }
}

/** add file snippets
 */
function snippets_add() {
    try {
        $app = Slim\Slim::getInstance();

        $name = $app->request->params('name'); // name snippets
        $star = $app->request->params('star'); // name snippets

        $gii = new easyuigii();
        $return = $gii->add_snippets($name); // return name without extension
        if ($return !== false) {
            $gii->save_star($name, $star); //save star to file json
            $app->response()->body(json_encode($return));
        } else {
            $app->render(200, ['isError' => true, 'msg' => $gii->T('File già presente')]);
        }

        ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - add file snippets  ' . PHP_EOL . $sql . PHP_EOL, 3, 'logs/error.log');
    }
}

/** delete file snippets
 */
function snippets_delete() {
    try {
        $app = Slim\Slim::getInstance();

        $name = $app->request->params('id'); // name snippets

        $gii = new easyuigii();
        $return = $gii->delete_snippets($name); // return name without extension
        if ($return !== false) {
            $gii->save_star($name, null); //delete star
            $app->render(200, ['success' => true]);
        } else {
            $app->render(200, ['isError' => true, 'msg' => $gii->T('File non presente')]);
        }

        ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - delete file snippets  ' . PHP_EOL . $sql . PHP_EOL, 3, 'logs/error.log');
    }
}

/** rename file snippets
 */
function snippets_rename() {
    try {
        $app = Slim\Slim::getInstance();

        $file_to = $app->request->params('name'); // file to rename
        $file_from = $app->request->params('file'); // file name start
        $star = $app->request->params('star'); // file name start

        $gii = new easyuigii();
        $return = $gii->rename_snippets($file_from, $file_to); // return name without extension
        if ($return !== false) {
            $gii->save_star($file_to, $star); //save star to file json
            $gii->save_star($file_from, null); //delete
            $data = ["file" => $file_to, "name" => $return];
            $app->response()->body(json_encode($data));
        } else {
            //not rename
            $gii->save_star($file_to, $star); //save star to file json
            $data = ["file" => $file_to, "name" => $file_from, "star" => $star];
            $app->response()->body(json_encode($data));
            //$app->render(200, ['isError' => true, 'msg' => $gii->T('File già presente')]);
        }

        ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - rename file snippets  ' . PHP_EOL . $sql . PHP_EOL, 3, 'logs/error.log');
    }
}

/** upload imae snippets
 */
function upload_image() {
    $app = Slim\Slim::getInstance();

    $name_file = $app->request->params('name_file'); // file name start
    $gii = new easyuigii();
    $gii->upload_image($name_file);


    ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
}

/** delete upload image
 */
function del_upload_image() {
    try {
        $app = Slim\Slim::getInstance();
        $file = $app->request->params('file'); // file name start

        $gii = new easyuigii();
        $filename = $gii->root_gii . "/snippets/image/" . $file . ".jpg";
        if (file_exists($filename)) {
            unlink($filename);
            $app->render(200, ['success' => true, 'msg' => $gii->T("E' stato cancellato il file"), 'title' => 'info']);
        } else {
            $app->render(200, ['success' => true, 'msg' => $gii->T("File non presente"), 'title' => 'warning']);
        }

        ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage(), 'title' => 'error']);
        error_log(LogTime() . 'error - delete uploadet image ' . PHP_EOL, 3, 'logs/error.log');
    }
}

/** Read from db
 *
 * @param type $table
 */
function dg_model_read_from_db($table) {
    try {
        $app = Slim\Slim::getInstance();

        $gii = new easyuigii();
        $gii->set_db_setting();
        $gii->table_name = $table;
        $data = $gii->get_table_model_from_db($table);

        $app->response()->body(json_encode($data));

        ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - read dg model  ' . PHP_EOL, 3, 'logs/error.log');
    }
}

function list_table_db() {
    try {
        $app = Slim\Slim::getInstance();

        $gii = new easyuigii();
        $gii->set_db_setting();
        $data = $gii->list_table_db();

        $app->response()->body(json_encode($data));

        ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - list table of db  ' . PHP_EOL, 3, 'logs/error.log');
    }
}

/** Save configuration crud
 */
function save_cfg2json() {
    try {
        $app = Slim\Slim::getInstance();
        $cfg = $app->request->params('cfg'); // cofiguration
        $cfg_name = $app->request->params('cfg_name'); // cofiguration
        $project_name = $app->request->params('project_name'); //

        $gii = new easyuigii();
        $gii->save_cfg_crud_to_json($cfg, $cfg_name, $project_name);

        $app->render(200, ['success' => true, 'msg' => $gii->T('Salvata configurazione')]);


        ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - save configuration to json  ' . PHP_EOL, 3, 'logs/error.log');
    }
}

/** list name configuration saved
 */
function list_cfg($folder) {
    try {
        $app = Slim\Slim::getInstance();

        $gii = new easyuigii();
        $data = $gii->list_configuration_saved($folder);
        $app->response()->body(json_encode($data));

        ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - list configuration ' . PHP_EOL, 3, 'logs/error.log');
    }
}

/** open configuration crud
 */
function open_cfg_from_json() {
    try {
        $app = Slim\Slim::getInstance();
        $cfg_name = $app->request->params('cfg_name'); // cofiguration
        $project_name = $app->request->params('project_name'); // cofiguration

        $gii = new easyuigii();
        $data = $gii->open_cfg_crud_from_json($cfg_name, $project_name);

        $app->render(200, ['success' => true, 'msg' => 'Configurazione caricata', 'cfg' => $data]);


        ($gii->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - open configuration from json  ' . PHP_EOL, 3, 'logs/error.log');
    }
}

/** set default with form length (from setting)
 */
function set_width_form() {
    try {
        $app = Slim\Slim::getInstance();
        $model = $app->request->params('model'); // cofiguration

        $gii = new easyuigii();
        $model = $gii->set_width_for_field_form_crud($model);

        $app->render(200, ['success' => true, 'model' => $model]);
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - set width form  ' . PHP_EOL, 3, 'logs/error.log');
    }
}

/** list project (folder)
 */
function list_project() {
    try {
        $app = Slim\Slim::getInstance();
        $model = $app->request->params('model'); // cofiguration

        $gii = new easyuigii();
        $data = $gii->list_project();

        $app->response()->body(json_encode($data));
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - list project  ' . PHP_EOL, 3, 'logs/error.log');
    }
}

/** get sql for crud
 */
function get_sql_crud() {
    try {
        $app = Slim\Slim::getInstance();
        $table = $app->request->params('table_name');
        $model = $app->request->params('model');

        $gii = new easyuigii();
        $data = $gii->get_sql_for_select($table, $model);

        $app->render(200, ['success' => true, 'sql' => $data]);
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - get sql for crud  ' . PHP_EOL, 3, 'logs/error.log');
    }
}

/** get sql for combo
 */
function get_sql_combo() {
    try {
        $app = Slim\Slim::getInstance();
        $table = $app->request->params('table_name');

        $gii = new easyuigii();
        $gii->set_db_setting();
        $model_combo = $gii->get_table_model_from_db($table);
        $data = $gii->get_sql_for_select($table, $model_combo);

        $app->render(200, ['success' => true, 'sql' => $data]);
    } catch (Exception $e) {
        $app->render(200, ['isError' => true, 'msg' => $e->getMessage()]);
        error_log(LogTime() . 'error - get sql for combo' . PHP_EOL, 3, 'logs/error.log');
    }
}
