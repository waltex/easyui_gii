<?php

//namespace easyuigii;

class easyuigii {

    private $template_base_path = "/src/template/base";
    private $template_root_path = "/src/template";
    private $root_gii = ""; // path root easyui gii
    private $primary_key = ""; // auto find from table structure
    private $app_setting = []; // array app setting from json file
    private $host_api = "api"; // for remote/local host es. (local) api or remote) http:/192.168.20/easui_gii/api
    private $current_languange = ''; //from app_setting.json
    public $debug_on_file = ''; //from app_setting.json
    private $oci_cn = ""; //current connection string for driver  oracle (oci)
    private $oci_user = ""; //current user for driver  oracle (oci)
    private $oci_password = ""; //current psw for driver oracle (oci)
    private $oci_name = ""; //name connection
    private $oci_charset = "";
    private $oci_production = "";
    private $oci_cn_var = "";
    private $oci_user_var = ""; //for code generated
    private $oci_password_var = ""; //for code generated
    public $app_name = "";
    public $app_folder = "";
    public $table_name = "";
    public $date_format = "DD-MM-YYYY";
    public $html_prefix = "1";
    public $api_url = "/crud/ABB_CRUD";
    public $api_fn_name = "crud_ABB_CRUD";
    public $dg_col_px_auto = true; //auto calc px, if false not set with length for datagrid col
    public $table_model = []; //tabel model structure

    function __construct() {
        $this->root_gii = str_replace('/src/class', '', str_replace('\\', '/', __DIR__)); //apllication path
        $this->set_app_setting(); //set to class method the array of setting
        $this->limit_size_log();

        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    }

    private function on_begin_crud() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $this->set_db_setting();

        $this->table_model = $this->get_table_model(true); // true get from custom model, false from db
        $this->primary_key = $this->get_primary_key_from_model();
    }



        /** read model table from db
     * @return type
     */
    public function read_dg_model_from_db() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
        $data = $this->get_table_model(false);
        return $data;
    }

    /** read model table from db
     * @return type
     */
    public function read_dg_model_from_json() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $file = $this->root_gii . $this->template_root_path . "/crud/model/custom_model.json";
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        return $data;
    }

    public function upload_image($name_file) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $uploadfile = $this->root_gii . "/snippets/image/" . $name_file . ".jpg";
        $filetmp = $_FILES['file']['tmp_name'];
        move_uploaded_file($filetmp, $uploadfile);
    }

    /** save star of the snippets
     *
     * @param type $file filename
     * @param type $star
     */
    public function save_star($file, $star) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
        $star = round($star);

        $file_star = $this->root_gii . "/snippets/star.json";

        if (file_exists($file_star)) {
            $ar_star = file_get_contents($file_star);
            $ar_file_star = json_decode($ar_star, true);
        } else {
            $ar_file_star = [];
        }
        if ($star == null) {
            unset($ar_file_star[$file]); //delete
        } else {
            $ar_file_star[$file] = $star;
        }
        $json = json_encode($ar_file_star);
        file_put_contents($file_star, $json);
    }

    /** rename  file snippets
     * @param type $name
     */
    public function rename_snippets($file_from, $file_to) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $dir = $this->root_gii . "/snippets";
        $filename_to = $dir . '/' . $file_to;
        $filename_from = $dir . '/' . $file_from;
        $filename_img_to = $dir . '/image/' . $file_to . ".jpg";
        $filename_img_from = $dir . '/image/' . $file_from . ".jpg";


        if ((file_exists($filename_from)) && (!file_exists($filename_to))) {
            copy($filename_from, $filename_to);
            unlink($filename_from);
            //rename image
            if ((file_exists($filename_img_from)) && (!file_exists($filename_img_to))) {
                copy($filename_img_from, $filename_img_to);
                unlink($filename_img_from);
            }

            $path_info = pathinfo($filename_to);
            $name = $path_info['filename'];
            return $name;
        } else {
            return false;
        }
    }

    /** delete  file snippets
     * @param type $name
     */
    public function delete_snippets($name) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $dir = $this->root_gii . "/snippets";
        $file = $dir . '/' . $name;


        if (file_exists($file)) {
            unlink($file);
        } else {
            return false;
        }
    }

    /** write  file snippets empty
     * @param type $name
     */
    public function add_snippets($name) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $filename = $name;

        $dir = $this->root_gii . "/snippets";
        $file = $dir . '/' . $name;

        if (!file_exists($file)) {
            $path_info = pathinfo($file);
            $name = $path_info['filename'];
            $ext = isset($path_info['extension']) ? $path_info['extension'] : "";
            $content = "";


            $html = trim('
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Snippets</title>
    <script src="js/asset.js" type="text/javascript"></script>
</head>
<html>
    <body>

    <script></script>
    </body>
</html>
                ');
            ($ext == "php") ? $content = "<?php" : false;
            (in_array($ext, ["htm", "html", "js"])) ? $content = $html : false;
            file_put_contents($file, $content);


            return ["file" => $filename, "name" => $name]; //su add not return the star
        } else {
            return false;
        }
    }

    /** lsit file on the folder snippets
     *
     * @param type $filter
     * @return type
     */
    public function list_file_for_snippets($filter, $ext, $filter_content) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $dir = $this->root_gii . "/snippets/";
        if (!is_dir($dir . 'image')) {
            mkdir($dir . 'image', 0777, true); //create folder
        }
        $list = scandir($dir);
        $data = [];
        foreach ($list as $file) {
            if (!in_array($file, ["..", ".", "image", ".DS_Store", "star.json"])) {
                $path_info = pathinfo($file);
                $name = $path_info['filename'];
                $data[] = ["file" => $file, "name" => $name];
            }
        }
        //$data = array_filter($data, 'self::filter_file');

        $data = array_filter($data, function($value) use ($filter, $ext, $filter_content) {
            return self::filter_file($value, $filter, $ext, $filter_content);
        });

        $file_star = $this->root_gii . "/snippets/star.json";
        if (file_exists($file_star)) {
            $ar_star = file_get_contents($file_star);
            $ar_file_star = json_decode($ar_star, true);
            $data2 = [];
            foreach ($data as $value) {
                $file = $value["file"];
                $name = $value["name"];
                $star = 0;
                if (array_key_exists($file, $ar_file_star)) {
                    $star = $ar_file_star[$file];
                    $star = round($star);
                }
                $data2[] = ["file" => $file, "name" => $name, "star" => $star];
            }
            return $data2;
        } else {
            return $data;
        }
    }

    /** filter array return for datagrid
     * @param type $value value to filter
     * @param type $filter word filter
     * @param type $ext_find estensiond to filter
     * @return type value find
     */
    private function filter_file($value, $filter, $ext_find, $filter_content) {
        $name = $value["name"]; //file without extenstion
        $file = $value["file"];
        $path_info = pathinfo($file);

        if ($filter_content === "true") {
            $file_content = $this->root_gii . '/snippets/' . $file;
            $text = file_get_contents($file_content); //filter the content file and not the name file
        } else {
            $text = $name; //filter the name file
        }

        if (array_key_exists('extension', $path_info)) {
            $ext_file = $path_info['extension'];
        } else {
            $ext_file = "";
        }
        ($ext_find == "???") ? $ext_find = null : false;

        if ($filter != "") {
            $ar_filter = explode(" ", $filter);
            $is_find = false;
            foreach ($ar_filter as $find) {
                if (strpos($text, $find) !== false) {
                    $is_find = true;
                }
            }
        } else {
            $is_find = true; //with no filter, show all
        }
        //filter extension
        if ($ext_find != "*") {
            if ($ext_file != $ext_find) {
                $is_find = false;
            }
        }
        if ($is_find) {
            return $value;
        }
    }

    /** get key  parameter of setting unless #1
     * @param type $value
     * @return type string
     */
    private function get_key_db_setting_no_id($value) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $pos = strpos($value, " ");
        $param = substr($value, $pos + 1, strlen($value));
        return $param;
    }

    /** get number parameter of setting db es. #1
     * @param type $value
     * @return type string es. #1
     */
    private function get_n_db_setting($value) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $pos = strpos($value, " ");
        $param = substr($value, 0, $pos);
        return $param;
    }

    /** set db setting to method class
     */
    public function set_db_setting() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $setting = $this->app_setting;
        $current_db = $setting["connessione database predefinita"];

        $ar_db = [];
        //set list id es.#1,#2,#3
        foreach ($setting as $key => $value) {
            $find = $this->get_n_db_setting($key);
            if ($find == $current_db) {
                $key_no_id = $this->get_key_db_setting_no_id($key);
                $ar_db[$key_no_id] = $value;
            }
        }
        if (array_key_exists("nome connessione database ORACLE (oci driver)", $ar_db)) {
            $this->oci_name = $ar_db["nome connessione database ORACLE (oci driver)"]; //user
            $this->oci_cn = $ar_db["tnsnames.ora"]; //
            $this->oci_user = $ar_db["utente database"]; //user
            $this->oci_password = $ar_db["password database"]; //password
            $this->oci_charset = $ar_db["codifica charset"]; //charset
            $this->oci_production = $ar_db["in produzione"]; //in production
            //for code generated
            $this->oci_user_var = $ar_db["variabile utente"]; //user var
            $this->oci_password_var = $ar_db["variabile password"]; //password var
            $this->oci_cn_var = $ar_db["variabile stringa di connessione"]; //name var of tsname.ora
        }
    }

    /** limit log to megabyte of setup
     */
    private function limit_size_log() {
        $dir = "logs/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); //create folder
        }
        $param_log = [
            "api.log" => str_replace(",", ".", $this->app_setting["limita file api.log a MB"]),
            "errore.log" => str_replace(",", ".", $this->app_setting["limita file error.log a MB"]),
            "debug.log" => str_replace(",", ".", $this->app_setting["limita file api.log a MB"]),
            "fn.log" => str_replace(",", ".", $this->app_setting["limita file fn.log a MB"]),
            "sql.log" => str_replace(",", ".", $this->app_setting["limita file sql.log a MB"]),
        ];


        foreach ($param_log as $file => $value) {
            $file = $dir . $file;
            if (($value > -1) > ($value != null)) {
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
    }

    /** set array app setting
     */
    public function set_app_setting() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $file = $this->root_gii . "/app_setting.json";
        $imp = file_get_contents($file);
        $ar_file = json_decode($imp, true);
        $this->app_setting = $ar_file;
        $this->current_languange = $ar_file["lingua corrente"];
        $this->debug_on_file = $ar_file["debug su file"];
    }

    /** folder create and file
     */
    public function build_app_crud() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $this->on_begin_crud();

        $dir = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->app_folder; //code path output
        $this->create_folder($dir);

        //build template html
        $root_template = $this->root_gii . $this->template_root_path;
        $loader = new Twig_Loader_Filesystem($root_template);
        $twig = new Twig_Environment($loader);
        //Transalte Tamplate with Function T - Very Super
        $function = new Twig_SimpleFunction('T', function ($value) {
            return $this->T($value);
        });
        $twig->addFunction($function);

        $this->copy_file_framework($dir); // copy file/folder framework
        //create asset template
        $js = $twig->render('/base/js/asset.js.twig', array(
            'language_default' => $this->current_languange  // current language
        ));
        $file = $dir . "/js/asset.js";
        file_put_contents($file, $js); //write generated html
        //create page js and html
        $html = $twig->render('/base/index.html.twig', array('url_body' => 'crud/body.crud.html.twig'
            , 'n' => $this->html_prefix
            , 'app_name' => $this->app_name
        ));
        $file = $dir . "/index.html";
        file_put_contents($file, $html); //write generated html

        $api_url = $this->get_api_name($this->api_url . "/:command", $this->api_fn_name); //create code url api + function
        $fn_api = $this->get_api_fn_crud($this->api_fn_name); //template redered api function

        $js = $twig->render('/crud/index.crud.js.twig', array('n' => $this->html_prefix
            , 'host_api' => $this->host_api
            , 'api_url' => $this->api_url
            , 'title' => $this->app_name
            , 'col_crud' => $this->get_template_js_crud() //this function use after $this->get_api_fn_crud
            , 'pk' => $this->primary_key
        ));
        $file = $dir . "/js/index.js";
        file_put_contents($file, $js); //write generated html
        //write template api_setup.php
        $api_setup = $twig->render('/base/api/api_setup.oci.php.twig', array(
            'oci_cn_var' => $this->oci_cn_var
            , 'oci_user_var' => $this->oci_user_var
            , 'oci_password_var' => $this->oci_password_var
            , 'oci_cn' => $this->oci_cn
            , 'oci_user' => $this->oci_user
            , 'oci_password' => $this->oci_password
        ));
        $file = $dir . "/api/api_setup.php";
        file_put_contents($file, $api_setup); //write generated html
        //create api
        $this->set_api($dir, $api_url, $fn_api); //create file api
    }

    /** cretate code for crud type column
     *
     * @return string es. "columns: [[ {field: 'ck', checkbox: true}, {field: 'ID', title: 'ID', width: '30px'
     */
    private function get_template_js_crud() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $model = $this->table_model;
        $key_row = [];
        $ar2 = []; //ord aray with ID/PRIMARY KEY with first element
        //while ($row = current($ar)) {
        foreach ($model as $value) {
            if ($value["CONSTRAINT_TYPE"] == "PRIMARY_KEY") {
                $key_row = $value;
            } else {
                $ar2[] = $value;
            }
        }
        $model_ord = array_merge([$key_row], $ar2); //model order by primary key

        $code = "";
        foreach ($model_ord as $value) {
            $col = $value["COL"];
            $type = $value["CONSTRAINT_TYPE"];
            if (($value["HIDE"] == "0") && ($value["SKIP"] == "0")) {
                $code.= $this->get_js_crud_col($value);
            }
        }
        $code = "columns: [[" . PHP_EOL . $code . PHP_EOL . "]]," . PHP_EOL;
        return $code;
    }

    /** return string code js for columns grid
     *
     *  es. {field: 'SEQ', title: 'Ord', width: '25px', editor: {type: 'numberbox', options: {required: true}}, sortable: true},
     *
     * @param type $col string name column
     */
    private function get_js_crud_col($row) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $col = $row["COL"];
        $type = $row["TYPE"];
        $type_pk_fk = $row["CONSTRAINT_TYPE"];
        $pk = $this->primary_key;

        $with = "";
        $colt = $this->T($col); //translate
        if ($this->dg_col_px_auto) {
            $px = (strlen($colt) * 15) . "px";
            $with = "width: '$px',";
        }

        if ($type_pk_fk == "PRIMARY_KEY") {
            $ck = "{field: 'ck', checkbox: true}," . PHP_EOL;
            return $ck . "{field: '$col', title: '$colt', $with sortable: true}," . PHP_EOL;
        }

        if ($row["CK"] == "1") {
            //{field: 'ID_CLONE', title: 'Fase<br>Duplicata', formatter: mycheck},
            return "{field: '$col', title: '$colt', editor: {type: 'checkbox', options: {on: '1', off: '0'}},formatter: mycheck,required: true}," . PHP_EOL;
        }


        if ($type == "textbox") {
            return "{field: '$col', title: '$colt', $with editor: {type: 'textbox', options: {required: true}}, sortable: true}," . PHP_EOL;
        }

        //escludo the column primary key for edit
        if ($type == "numberbox") {
            $with = "width: '50px',";
            return "{field: '$col', title: '$colt', $with editor: {type: 'numberbox', options: {required: true}}, sortable: true}," . PHP_EOL;
        }
  
        if ($type == "datebox") {
            $with = "width: '100px',";
            ($this->date_format = "DD-MM-YYYY") ? $type_dt = "it" : $type_dt = "en";
            $date_format = "formatter: myformatter_d_$type_dt, parser: myparser_d_$type_dt,";
            return "{field: '$col', title: '$colt', $with editor: {type: 'datebox', options: { $date_format required: true}}, sortable: true}," . PHP_EOL;
        }
        return "{field: '$col', title: '$colt', $with editor: {type: '??$type??', options: {required: true}}, sortable: true}," . PHP_EOL;
    }

    /** create sql string
     *
     * @return type string
     */
    private function get_sql_for_select() {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $app = Slim\Slim::getInstance();

            $table = $this->table_name;

            $str_col_w_a = "";
            $str_col = "";
            $ncol = 0;
            $model = $this->table_model;
            foreach ($model as $value) {
                $col_name = $value["COL"];
                $col_name_w_a = "A." . $col_name;
                $col_type = $value["TYPE"];
                //skip cols
                if ($value["SKIP"] == "0") {
                    $ncol+=1;
                    if ($col_type == "datebox") {
                        $col_name_w_a = $this->format_date_to_char($col_name_w_a, $col_name);
                    }
                    $strComma = ($ncol > 1) ? ", " : "";
                    $str_col_w_a.=$strComma . $col_name_w_a; //list col with alias
                    $str_col.=$strComma . $col_name; //list col without alias
                }
            }
            $strSql = "SELECT $str_col_w_a FROM $table A";
            return $strSql;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }



    /** get table model
     *
     * @param type $from_json if true get from custom model (json file)
     * @return type
     * @throws Exception
     */
    public function get_table_model($from_json) {
        try {

            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            if (!$from_json) {

                $app = Slim\Slim::getInstance();
                $table = $this->table_name;
                $sql = "
                SELECT
                 A.COLUMN_NAME COL
                 ,case A.DATA_TYPE when 'NUMBER' then  'numberbox'
                                   when 'VARCHAR' then  'textbox'
                                   when 'VARCHAR2' then 'textbox'
                                   when 'DATE' then 'datebox'
                                   else A.DATA_TYPE
                end TYPE
                , B.CONSTRAINT_TYPE
                , 0 SKIP
                , 0 HIDE
                , 0 CK
                FROM ALL_TAB_COLUMNS A LEFT JOIN
                (
                SELECT
                cols.column_name,
                case cons.constraint_type when 'P' then 'PRIMARY_KEY' when 'R' then 'FOREIGN_KEY' else  cons.constraint_type end CONSTRAINT_TYPE
                FROM all_constraints cons,
                all_cons_columns cols
                WHERE cols.table_name='$table'
                AND cons.constraint_name = cols.constraint_name
                AND cons.owner = cols.owner
                ) B on (A.COLUMN_NAME=b.COLUMN_NAME)
                WHERE table_name='$table'
                ";

                error_log(LogTime() . ' Sql, get model table: ' . PHP_EOL . $sql . PHP_EOL, 3, 'logs/sql.log');


                //$conn = oci_connect($db4_user, $db4_psw, $db4_GOLD, 'UTF8');
                $conn = oci_connect($this->oci_user, $this->oci_password, $this->oci_cn, $this->oci_charset);
                $db = oci_parse($conn, $sql);
                $rs = oci_execute($db);

                oci_fetch_all($db, $data, null, null, OCI_ASSOC + OCI_FETCHSTATEMENT_BY_ROW);


                //save json model
                $json = json_encode($data);
                $file = $this->root_gii . $this->template_root_path . "/crud/model/model_from_db.json";
                file_put_contents($file, $json);
                return $data;
            } else {
                $file = $this->root_gii . $this->template_root_path . "/crud/model/custom_model.json";
                $json = file_get_contents($file);
                $data = json_decode($json, true);
                return $data;
            }
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** get column name primary key
     * @return type strin column name primary key
     */
    private function get_primary_key_from_model() {
        $model = $this->table_model;
        foreach ($model as $value) {
            if ($value["CONSTRAINT_TYPE"] == "PRIMARY_KEY") {
                return $value["COL"];
            }
        }
    }

    /** get primary key
     *
     * @return type string primary key
     */
    private function get_primary_key() {
        try {

            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $app = Slim\Slim::getInstance();
            $table = $this->table_name;
            $sql = "
                SELECT cols.table_name,
                cols.column_name,
                cons.constraint_type
                FROM all_constraints cons,
                all_cons_columns cols
                WHERE cols.table_name='$table'
                and cons.constraint_type = 'P'   -- P primary , R forenk
                AND cons.constraint_name = cols.constraint_name
                AND cons.owner = cols.owner
                ORDER BY cols.table_name,cols.position
                ";

            error_log(LogTime() . ' Sql, get primary key: ' . PHP_EOL . $sql . PHP_EOL, 3, 'logs/sql.log');


            //$conn = oci_connect($db4_user, $db4_psw, $db4_GOLD, 'UTF8');
            $conn = oci_connect($this->oci_user, $this->oci_password, $this->oci_cn, $this->oci_charset);
            $db = oci_parse($conn, $sql);
            $rs = oci_execute($db);

            oci_fetch_all($db, $data, null, null, OCI_ASSOC + OCI_FETCHSTATEMENT_BY_ROW);

            if (Count($data) > 0) {
                $pk = $data[0]['COLUMN_NAME'];
                return $pk;
            } else {
                // error not find primary key
                throw new Exception($this->T('Errore - non è possibile recuperare la primary key'));
            }
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** to_char with date
     *
     * @param type $field field
     * @param type $col_name name column
     * @return type string es. (A.FIELD1,'YYYY-MM-DD') FIELD1
     */
    private function format_date_to_char($filed, $col_name) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        return "TO_CHAR($filed,'" . $this->date_format . "') $col_name";
    }

    /** to_char with date
     *
     * @param type $field field

     * @return type string es. (:FIELD1,'YYYY-MM-DD')
     */
    private function format_dt2todate($filed) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        return "TO_DATE($filed,'" . $this->date_format . "')";
    }

    /** get string code  fn CrudBase
     *
     * @param type $fn_name
     */
    private function get_api_fn_crud($api_fn_name) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $sql_select = $this->get_sql_for_select(); //for template
        $param_api_ins = $this->get_param_api_for_insert_update(false); //for template
        $sql_insert = $this->get_sql_for_insert(); //for template
        $param_log_insert = $this->get_param_sql_for_log_insert_update(false);
        $bind_insert = $this->get_param_for_bind_insert_update(false);
        $param_return = $this->get_param_insert_update_return();
        $param_api_upd = $this->get_param_api_for_insert_update(true); //for template
        $param_log_update = $this->get_param_sql_for_log_insert_update(true);
        $sql_update = $this->get_sql_for_update();
        $bind_update = $this->get_param_for_bind_insert_update(true);
        //build template html
        $root_template = $this->root_gii . $this->template_root_path;
        $loader = new Twig_Loader_Filesystem($root_template);
        $twig = new Twig_Environment($loader);

        $php = $twig->render('/crud/api/crud.api.oci.php.twig', array(
            'api_fn_name' => $api_fn_name
            , 'sql_select' => $sql_select
            , 'sql_insert' => $sql_insert
            , 'table' => $this->table_name
            , 'pk' => $this->primary_key
            , 'param_api_ins' => $param_api_ins
            , 'param_log_insert' => $param_log_insert
            , 'bind_insert' => $bind_insert
            , 'param_return' => $param_return
            , 'param_api_upd' => $param_api_upd
            , 'param_log_update' => $param_log_update
            , 'sql_update' => $sql_update
            , 'bind_update' => $bind_update
            , 'drv_cn_var' => $this->oci_cn_var
            , 'drv_user_var' => $this->oci_user_var
            , 'drv_password_var' => $this->oci_password_var
            , 'drv_charset' => $this->oci_charset
        ));
        $php = str_replace("<?php", "", $php);
        return $php;
    }

    /** list col from type col table es. field1, field2, field3
     */
    private function get_list_col() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $str_col = "";
        $ncol = "0";
        $model = $this->table_model;
        foreach ($model as $value) {
            $col_name = $value["COL"];
            if ($value["SKIP"] == 0) {
                $ncol+=1;
                $str_comma = ($ncol > 1) ? ", " : "";
                $str_col.=$str_comma . $col_name; //list col without alias
            }
        }
        return $str_col;
    }

    /** param for bind insert/update
     *
     * @return string paramsql es oci_bind_by_name($db, ":ID", $ID, OCI_B_ROWID);
     */
    private function get_param_for_bind_insert_update($isUpdate) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $str_bind = "";
        $model = $this->table_model;
        //while ($row = current($ar)) {
        foreach ($model as $value) {

            $col_name = $value["COL"];
            if ($value["SKIP"] == "0") {
                ($col_name == $this->primary_key) ? $type_param = "OCI_B_ROWID" : $type_param = "-1";
                ($isUpdate) ? $type_param = "-1" : false;
                $str_bind.="oci_bind_by_name(\$db, \":$col_name\", \$$col_name, $type_param);" . PHP_EOL;
            }
        }
        return $str_bind;
    }

    /** param for insert - return array
     *
     * @return string paramsql es 'PARAM1' => $PARAM1,
     */
    private function get_param_insert_update_return() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $str_par = "";
        $model = $this->table_model;
        foreach ($model as $value) {
            $col_name = $value["COL"];
            if ($value["SKIP"] == "0") {
                $str_par.= "'$col_name' => \$$col_name," . PHP_EOL;
            }
        }
        return $str_par;
    }

    /** param for insert for log sql
     *
     * @return string paramsql es ':PARAM1' => $PARAM1,
     */
    private function get_param_sql_for_log_insert_update($add_idd) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $str_par = "";
        $model = $this->table_model;
        //while ($row = current($ar)) {
        foreach ($model as $value) {
            $col_name = $value["COL"];
            if ($this->primary_key != $col_name) {
                if ($value["SKIP"] == "0") {
                    $str_par.="':" . $col_name . "' => $" . $col_name . "," . PHP_EOL;
                }
            } else { //add primary key
                if ($add_idd) {
                    if ($value["SKIP"] == "0") {
                        $str_par.="':" . $col_name . "' => $" . $col_name . "," . PHP_EOL;
                    }
                }
            }

        }
        return $str_par;
    }

    /** get sql for update
     */
    private function get_sql_for_update() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $str_col = "";
        $ncol = "0";
        $model = $this->table_model;
        foreach ($model as $value) {
            $col_name = $value["COL"];
            $col_type = $value["TYPE"];
            if ($this->primary_key != $col_name) {
                if ($value["SKIP"] == "0") {
                    $ncol+=1;
                    $str_comma = ($ncol > 1) ? ", " : "";

                    if ($col_type == "datebox") {
                        $col_dt = $this->format_dt2todate(":" . $col_name);
                        $col_name = "$col_name=$col_dt";
                    } else {
                        $col_name = "$col_name=:$col_name";
                    }
                    $str_col.=$str_comma . $col_name; //list col -> :field1, :field2
                }
            }
        }
        $table = $this->table_name;
        $pk = $this->primary_key;

        $sql = "UPDATE $table SET $str_col WHERE $pk=:$pk";
        return $sql;
    }

    /** list col from type col table for insert sql es. field1, field2, field3
     */
    private function get_list_col_for_insert_values() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $str_col = "";
        $ncol = "0";
        $model = $this->table_model;
        foreach ($model as $value) {

            $col_name = $value["COL"];
            $col_type = $value["TYPE"];
            if ($this->primary_key != $col_name) {
                if ($value["SKIP"] == "0") {
                    $ncol+=1;
                    $str_comma = ($ncol > 1) ? ", " : "";
                    $col_name = ":" . $col_name;
                    if ($col_type == "datebox") {
                        $col_name = $this->format_dt2todate($col_name);
                    }
                    $str_col.=$str_comma . $col_name; //list col -> :field1, :field2
                }
            }
        }
        return $str_col;
    }

    private function get_sql_for_insert() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $list_col = $this->get_list_col(); //field1, field2, field3
        $str_col = $this->get_list_col_for_insert_values(); //:field1, :field2
        $table = $this->table_name;
        $seq = $table . "_SEQ"; //sequenze
        $pk = $this->primary_key;

        $sql = "
                        DECLARE
                        ID_SEQ NUMBER(15,0) := NULL;
                        BEGIN
                            SELECT $seq.NEXTVAL INTO ID_SEQ FROM DUAL;
                            INSERT INTO $table ($list_col)
                            VALUES (ID_SEQ, $str_col)
                            RETURNING ID_SEQ  INTO :$pk;
                        END;
                    ";
        return $sql;
    }

    /** es . $ID = $app->request->params('ID'); // Param from Post user
     *
     * @param type $add_id if true add param for primary key
     * @return string
     * @throws Exception
     */
    private function get_param_api_for_insert_update($add_id) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        try {
            $code = "";
            $model = $this->table_model;
            foreach ($model as $value) {
                $col = $value["COL"];
                if ($value["SKIP"] == "0") {
                    if ($col != $this->primary_key) {
                        $code.= "\$$col= \$app->request->params('$col'); // Param from Post user" . PHP_EOL;
                    } else {
                        if ($add_id) { //primary key
                            $code.= "\$$col = \$app->request->params('$col'); // Param from Post user" . PHP_EOL;
                        }
                    }
                }
            }
            return $code;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** get string code  api name
     *
     * @param type $url endpoint url
     * @param type $fn function associate to api
     * @return type string
     */
    private function get_api_name($url, $fn) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        return '$' . "app->post('$url', '$fn'); ";
    }

    /** create rest api
     *
     * @param type $dir directory api
     * @param type $api_url string rest api url
     * @param type $fn_api string rest functions
     */
    private function set_api($dir, $api_url, $fn_api) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $file1_api = $this->root_gii . $this->template_base_path . "/api/api_1_declare.php";
        $api_declare = file_get_contents($file1_api);

        $file2_api = $this->root_gii . $this->template_base_path . "/api/api_2_fn.php";
        $api_fn = str_replace("<?php", "", file_get_contents($file2_api));

        $api = $api_declare . PHP_EOL . $api_url . PHP_EOL . $api_fn . PHP_EOL . $fn_api; //create File Api
        $file = $dir . "/api/api.php";
        file_put_contents($file, $api); //write api
    }

    /** copy file to directory
     * @param type $ar_files array files es. [[full_path_file_from, dir_to],[full_path_file_from, dir_to]....]

     */
    function copy_files_to_dir($ar_files) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        try {
            foreach ($ar_files as $file) {
                $from = $file[0];
                $to = $file[1] . basename($file[0]);
                copy($from, $to);
            }
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** copy file (css, js, ....) for app
     * @param type $dir directory app
     */
    private function copy_file_framework($dir) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        try {
            $this->create_folder($dir . "/api");

            $zip_file = $this->root_gii . $this->template_base_path . '/lib.zip';
            $this->unzip($zip_file, $dir);
            $zip_file = $this->root_gii . $this->template_base_path . '/css.zip';
            $this->unzip($zip_file, $dir);
            $zip_file = $this->root_gii . $this->template_base_path . '/js.zip';
            $this->unzip($zip_file, $dir);


            $zip_file = $this->root_gii . $this->template_base_path . '/vendor.zip';
            $this->unzip($zip_file, $dir . "/");

            $template_path = $this->root_gii . $this->template_base_path;
            $ar_files = [
                [$template_path . "/LICENSE", $dir . "/"],
                [$template_path . "/composer.json", $dir . "/"],
                [$template_path . "/.htaccess", $dir . "/"], //for disable cache javascript
                [$template_path . "/api/.htaccess", $dir . "/api/"],
                [$template_path . "/api/fn_api.php", $dir . "/api/"],
            ];
            $this->copy_files_to_dir($ar_files, $dir);
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** create folder recursive and delete file if exists
     * @param type $dir directory
     */
    private function create_folder($dir) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $ck = basename($dir);
        if ($ck != 'htdocs') {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true); //create folder
            } else {
                $this->rrmdir($dir); //delete file
                mkdir($dir, 0777, true); //create folder and folder below
            }
        } else {
            throw new Exception($this->T('Percorso erratto')); //Erratic Path
        }
    }

    /**
     * remove empty dir
     * @param string $dir directory
     */
    private function rrmdir($dir) {

        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        self::rrmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /** exract zip file to folder
     * @param string $zip_file zip file
     * @param string $dir directory to extract
     */
    private function unzip($zip_file, $dir) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $zip = new ZipArchive;
        $file = $zip->open($zip_file);
        $zip->extractTo($dir);
        $zip->close();
    }

    // metodi
    public function test() {
        echo "test class ";
    }

    /** translator function
     * @param type $value text to translate
     * @return type
     */
    public function T($value) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $ar_file = $this->app_setting;
        $lang2from = $ar_file["traduci dalla lingua"];
        $lang2to = $ar_file["traduci alla lingua"];
        $langDefault = $ar_file["lingua corrente"];

        $file = $this->root_gii . "/language/$lang2from" . "2" . "$lang2to.json";
        //write file if not exists
        if (!file_exists($file)) {
            file_put_contents($file, json_encode([]));
        }


        $imp = file_get_contents($file);
        $ar_file = json_decode($imp, true);

        //translate and add to dictionary if not exists
        if (!array_key_exists($value, $ar_file)) {
            $trans = new \Statickidz\GoogleTranslate();
            $value_t = $trans->translate($lang2from, $lang2to, $value);

            $ar_out = array_merge($ar_file, [$value => $value_t]);
            file_put_contents($file, json_encode($ar_out));
        } else {
            $value_t = $ar_file[$value]; //get translation from dictionary file
        }

        return ($langDefault == $lang2from) ? $value : $value_t;
    }

}
