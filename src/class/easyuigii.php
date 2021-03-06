<?php

//namespace easyuigii;

class easyuigii {

    private $template_base_path = "/src/template/base";
    private $template_root_path = "/src/template";
    private $template_excel_path = "/src/template/excel";
    private $root_gii = ""; // path root easyui gii
    private $primary_key = ""; // auto find from table structure
    private $app_setting = []; // array app setting from json file
    private $host_api = "api"; // for remote/local host es. (local) api or remote) http:/192.168.20/easui_gii/api
    private $current_languange = ''; //from app_setting.json
    public $debug_on_file = ''; //from app_setting.json
    private $current_driver = ""; // oci | odbc | pdo
    private $type_db = ""; //oracle | mysql | mssql
    private $oci_cn = ""; //current connection string for driver  oracle (oci)
    private $oci_user = ""; //current user for driver  oracle (oci)
    private $oci_password = ""; //current psw for driver oracle (oci)
    private $oci_name = ""; //name connection
    private $oci_charset = "";
    private $oci_production = ""; //true|false
    private $oci_cn_var = "";
    private $oci_user_var = ""; //for code generated
    private $oci_password_var = ""; //for code generated
    private $odbc_cn = ""; //current connection string for driver odbc
    private $odbc_user = ""; //current user for driver odbc
    private $odbc_password = ""; //current psw for driver odbc
    private $odbc_name = ""; //name connection
    private $odbc_production = ""; //true|false
    private $odbc_cn_var = "";
    private $odbc_user_var = ""; //for code generated
    private $odbc_password_var = ""; //for code generated
    private $pdo_cn = ""; //current connection string for driver pdo
    private $pdo_user = ""; //current user for driver pdo
    private $pdo_password = ""; //current psw for driver pdo
    private $pdo_options_var = ""; //var for pdo option
    private $pdo_options = ""; //pdo option for code generated
    private $pdo_options_gii = [
        PDO::ATTR_EMULATE_PREPARES => false, // turn off emulation mode for "real" prepared statements
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
    ]; // for pdo gii
    private $pdo_name = ""; //name connection
    private $pdo_production = ""; //true|false
    private $pdo_cn_var = "";
    private $pdo_user_var = ""; //for code generated
    private $pdo_password_var = ""; //for code generated
    public $app_name = "";
    public $app_folder = "";
    public $table_name = "";
    public $model_from_json = 0; // 1 for custom model, 0 from db
    public $table_model = []; //tabel model structure
    public $date_format = "DD-MM-YYYY";
    public $html_prefix = "";
    public $ck_title = 0; //1 enable title toolbar datagrid
    public $title = ""; // title toolbar datagrid
    public $pagination = 0;
    public $pagination_list = "";  //string list es. [25,50]
    public $pagination_size = "";
    public $dg_inline = 1; // 1 edit in line , 0 edit on form
    public $width_form = ""; // width for crud modal form
    public $form_full = 0; // 1 open form on full screen on opened
    public $row_num = 0; // 1 show numeric row on datagrid
    public $height_form = ""; // height for crud modal form
    public $filter_base = 0; // 1 enable simple filter on column
    public $ck_custom_sql = 0; //enable insert custom sql for select, befor filter
    public $custom_sql = ""; // text  form custom sql for select, berfort filter
    public $ck_custom_sql2 = 0; //enable insert custom sql for select, after filter
    public $custom_sql2 = ""; // text  form custom sql for select, after filter
    public $ck_global_var = 0; //enable global var
    public $global_var = ""; // global var
    public $sql_alias = "A"; // es A  -> A.COLNAME
    public $ck_sql_alias = ""; // 1 enable alias for col sql
    public $ck_row_styler = 0; //chewck for enable  code for rowstyler
    public $row_styler = ""; // code for rowstyler
    public $group_col = ""; // column for group data of datagrid
    public $lock_col = ""; // lock scroll column of datagrid
    public $ck_load_dg = 1; // carica dati a avvio
    public $crud = ['C', 'R', 'U', 'D']; // abilitazioni
    private $crud_c = 0; //create
    private $crud_r = 0; //read
    private $crud_u = 0; //update
    private $crud_d = 0; //delete
    private $enable_filter_dg = 0; // true -> is enabled advanced filter
    private $str_filter_dg = ""; // conditional string for add filter to sql
    public $ck_model_xls = 1; // 1 enable export to excel. 0 only csv with javascript
    public $model_xls = []; // array model for param for export excel

    function __construct() {
        $this->root_gii = str_replace('/src/class', '', str_replace('\\', '/', __DIR__)); //apllication path
        //chmod($this->root_gii, 0777);
        $this->create_folder_gii();
        $this->set_app_setting(); //set to class method the array of setting
        $this->limit_size_log();

        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
    }

    private function create_folder_gii() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $list_dir = ["cfg", "snippets", "src/template/crud/model"];
        foreach ($list_dir as $dir) {
            $root = $this->root_gii . "/";
            if (!is_dir($root . $dir)) {
                mkdir($root . $dir, 0777, true); //create folder
            }
        }
    }

    private function on_begin_crud() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $this->set_db_setting();
        $this->se_var_command_crud();

        if ($this->model_from_json == 0) {
            $this->table_model = $this->get_table_model_from_db($this->table_name);
        }
        $this->primary_key = $this->get_primary_key_from_model();
    }

    private function se_var_command_crud() {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $crud = $this->crud;
            foreach ($crud as $value) {
                ($value == "C") ? $this->crud_c = 1 : false;
                ($value == "R") ? $this->crud_r = 1 : false;
                if ($value == "U") {
                    $this->crud_u = 1;
                    //$dg_inline = 1;
                }

                ($value == "D") ? $this->crud_d = 1 : false;
            }
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** list table of db
     * @return type
     */
    public function list_table_db() {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            if ($this->current_driver == "oci") {
                $sql = "
                SELECT table_name TEXT FROM dba_tables WHERE OWNER=user ORDER BY 1
                ";
                error_log(LogTime() . ' Sql, get list table of db: ' . PHP_EOL . $sql . PHP_EOL, 3, 'logs/sql.log');


                $conn = oci_connect($this->oci_user, $this->oci_password, $this->oci_cn, $this->oci_charset);
                $db = oci_parse($conn, $sql);
                $rs = oci_execute($db);

                oci_fetch_all($db, $data, null, null, OCI_ASSOC + OCI_FETCHSTATEMENT_BY_ROW);
                return $data;
            }

            if ($this->current_driver == "odbc") {
                $dbh = odbc_connect($this->odbc_cn, $this->odbc_user, $this->odbc_password);
                $result = odbc_tables($dbh);
                return $result;
            }

            if (($this->current_driver == "pdo") && ($this->type_db == "mysql")) {
                $tableList = [];
                $dbh = new PDO($this->pdo_cn, $this->pdo_user, $this->pdo_password, $this->pdo_options_gii);
                $result = $dbh->query("SHOW TABLES");
                while ($row = $result->fetch(PDO::FETCH_NUM)) {
                    $tableList[]["TEXT"] = $row[0];
                }
                return $tableList;
            }
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** set width field form for form crud 
     *
     * @param type $json model
     * @return type
     */
    public function set_width_for_field_form_crud($json_model) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $par_dt = $this->app_setting["larghezza campo datebox sul form"];
        $par_combo = $this->app_setting["larghezza campo combox sul form"];
        $par_text = $this->app_setting["larghezza campo textbox sul form"];
        $par_number = $this->app_setting["larghezza campo numberbox sul form"];
        $par_onoff = $this->app_setting["larghezza campo  si/no sul form"];

        $model = [];
        foreach ($json_model as $value) {
            $value_field = "";
            ($value["TYPE"] == "datebox") ? $value_field = $par_dt : false;
            ($value["TYPE"] == "combobox") ? $value_field = $par_combo : false;
            (($value["TYPE"] == "textbox") || ($value["TYPE"] == "textarea")) ? $value_field = $par_text : false;
            ($value["TYPE"] == "numberbox") ? $value_field = $par_number : false;
            ($value["CK"] == "1") ? $value_field = $par_onoff : false;
            $value["WIDTH_FORM"] = $value_field;
            $model[] = $value;
        }
        return $model;
    }

    /** set flag hide of model  with array param asscociation es -> DT_INS;DT_MOD
     *
     * @param type $json
     * @return type
     */
    private function set_flag_hide_model_from_ar_setting($json) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $value = $this->app_setting["imposta il flag scartato sul campo del modello CRUD"];
        $ar_field = explode("|", $value);
        $model = [];
        foreach ($json as $value) {
            $col_find = $value["COL"];
            if (in_array($col_find, $ar_field)) {
                $value["SKIP"] = "1";
                $model[] = $value;
            } else {
                $model[] = $value;
            }
        }
        return $model;
    }

    /** set flag on/off of model  with array param asscociation es -> ACTIVED;CK
     *
     * @param type $json
     * @return type
     */
    private function set_flag_onoff_model_from_ar_setting($json) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $value = $this->app_setting["imposta il flag on/off sul campo del modello CRUD"];
        $ar_field = explode("|", $value);
        $model = [];
        foreach ($json as $value) {
            $col_find = $value["COL"];
            if (in_array($col_find, $ar_field)) {
                $value["CK"] = "1";
                $model[] = $value;
            } else {
                $model[] = $value;
            }
        }
        return $model;
    }

    /** replace title of model  with array param asscociation es ID ->#, DTIN ->Data inizio
     *
     * @param type $json
     * @return type
     */
    private function set_title_model_from_ar_setting($json) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $ar_title = $this->app_setting["imposta il titolo sul campo del modello CRUD"];
        $ar_field = explode("|", $ar_title);
        $ar_title = [];
        foreach ($ar_field as $value) {
            $data = explode(":", $value);
            $key = $data[0];
            $value = $data[1];
            $ar_title[$key] = $value;
        }
        $model = [];
        foreach ($json as $value) {
            $new_value = $this->replace_title_of_model_row($value, $ar_title);
            $model[] = $new_value;
        }
        return $model;
    }

    /** replace title of model row with array param asscociation es ID ->#, DTIN ->Data inizio
     *
     * @param type $row
     * @param type $ar_title
     * @return type
     */
    private function replace_title_of_model_row($row, $ar_title) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
        foreach ($row as $value) {
            if (array_key_exists($value, $ar_title)) {
                if (($row["COL"] == $row["TITLE"]) || ($row["TITLE"] == "") || ($row["TITLE"] == null)) {
                    $key = $row["COL"];
                    $new_val = $ar_title[$key];
                    $row["TITLE"] = $new_val;
                }
                return $row;
            } else {
                return $row;
            }
        }
    }

    /**
     * @param type $cfg_name file configuration crud
     * @param type $project_name name project
     * @return type
     */
    public function open_cfg_crud_from_json($cfg_name, $project_name) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $file = $this->root_gii . "/cfg/$project_name/" . $cfg_name . ".json";

        $json = file_get_contents($file);
        $data = json_decode($json, true);
        return $data;
    }

    /** savve configuration crud
     *
     * @param type $cfg configuration array
     * @param type $cfg_name name configurayion
     */
    public function save_cfg_crud_to_json($cfg, $cfg_name, $project_name) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $dir = $this->root_gii . "/cfg/$project_name/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); //create folder
        }

        $file = $this->root_gii . "/cfg/$project_name/" . $cfg_name . ".json";
        $json = json_encode($cfg);
        file_put_contents($file, $json);
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

    /** list project
     * @return type
     */
    public function list_project() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $dir = $this->root_gii . "/cfg/";
        if (!is_dir($dir)) {
            //mkdir($dir . 'cfg', 0777, true); //create folder
        }
        $list = scandir($dir);
        $data = [];
        foreach ($list as $file) {
            if (!in_array($file, ["..", ".", ".DS_Store"])) {
                if (is_dir($dir . $file)) {
                    $path_info = pathinfo($file);
                    $name = $path_info['filename'];
                    $data[] = ["folder" => $name,];
                }
            }
        }
        return $data;
    }

    /** list configuration saved
     *
     * @param type $folder project name
     * @return type
     */
    public function list_configuration_saved($folder) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $dir = $this->root_gii . "/cfg/$folder/";
        $list = scandir($dir);
        $data = [];
        foreach ($list as $file) {
            if (!in_array($file, ["..", ".", ".DS_Store"])) {
                $path_info = pathinfo($file);
                $name = $path_info['filename'];
                $data[] = ["file" => $name,];
            }
        }
        return $data;
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
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $pos = strpos($value, " ");
            $param = substr($value, 0, $pos);
            return $param;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** set db setting to method class
     */
    public function set_db_setting() {
        try {
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
                $this->current_driver = "oci";
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

            if (array_key_exists("nome connessione database (ODBC driver)", $ar_db)) {
                $this->current_driver = "odbc";
                $this->odbc_name = $ar_db["nome connessione database (ODBC driver)"]; //user
                $this->odbc_cn = $ar_db["stringa di connessione ODBC"]; //
                $this->odbc_user = $ar_db["utente database"]; //user
                $this->odbc_password = $ar_db["password database"]; //password
                $this->odbc_production = $ar_db["in produzione"]; //in production
                //for code generated
                $this->odbc_user_var = $ar_db["variabile utente"]; //user var
                $this->odbc_password_var = $ar_db["variabile password"]; //password var
                $this->odbc_cn_var = $ar_db["variabile stringa di connessione"]; //name var of tsname.ora
            }

            if (array_key_exists("nome connessione database (PDO driver)", $ar_db)) {
                $this->current_driver = "pdo";
                $this->type_db = $ar_db["tipo database"]; //type db
                $this->pdo_name = $ar_db["nome connessione database (PDO driver)"]; //connection string
                $this->pdo_cn = $ar_db["stringa di connessione PDO"]; //
                $this->pdo_user = $ar_db["utente database"]; //user
                $this->pdo_password = $ar_db["password database"]; //password
                $this->pdo_options = $ar_db["parametri PDO"]; //option Pdo
                $this->pdo_production = $ar_db["in produzione"]; //in production
                //for code generated
                $this->pdo_user_var = $ar_db["variabile utente"]; //user var
                $this->pdo_password_var = $ar_db["variabile password"]; //password var
                $this->pdo_cn_var = $ar_db["variabile stringa di connessione"]; //name var of tsname.ora
                $this->pdo_options_var = $ar_db["variabile opzioni connessione PDO"]; //name var of tsname.ora
            }
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
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

        $desc_title = $this->title;
        $title = ($this->ck_title == 1) ? "title:'$desc_title'," : "";

        $this->set_enable_filter_dg();

        $url_api_crud = "/crud/" . $this->table_name;
        $api_fn_name_crud = "crud_" . $this->table_name;
        $url_export_xls = "api/data/$api_fn_name_crud.xls"; //associo il nome della funzione dell api x rendere il file univoco
        //create page js and html
        $html = $twig->render('/base/index.html.twig', array('url_body' => 'crud/body.crud.html.twig'
            , 'n' => $this->html_prefix
            , 'app_name' => $this->app_name
            , 'dg_inline' => $this->dg_inline
            , 'group_col' => $this->group_col
            , 'crud_c' => $this->crud_c
            , 'crud_r' => $this->crud_r
            , 'crud_u' => $this->crud_u
            , 'crud_d' => $this->crud_d
            , 'enable_filter' => $this->enable_filter_dg
            , 'title' => $title
            , 'url_export_xls' => $url_export_xls
            , 'ck_model_xls' => $this->ck_model_xls
        ));
        $file = $dir . "/index.html";
        file_put_contents($file, $html); //write generated html



        $api_url = $this->get_api_name($url_api_crud . "/:command", $api_fn_name_crud); //create code url api + function
        $api_url_combo = $this->get_api_name_for_combobox();
        $api_url = array_merge([$api_url], $api_url_combo);

        $fn_api = $this->get_api_fn_crud($api_fn_name_crud); //template redered api function
        $fn_combo = $this->get_api_fn_combo();
        $fn_api = array_merge([$fn_api], $fn_combo);
        $pagination = $this->get_pagination();
        $on_after_edit = $this->get_crud_js_fn__on_after_edit();

        $hide_id_ins = $this->get_id_hide_for_dg_edit_form();
        $input_cell = $this->get_input_cell_for_dg_edit_form();
        $options_obj = $this->get_option_for_dg_edit_form();

        //for filter
        $input_cell_filter = ($this->enable_filter_dg == 1) ? $this->get_input_cell_for_dg_filter_form() : "";
        $options_obj_filter = ($this->enable_filter_dg == 1) ? $this->get_option_for_dg_filter_form() : ""; //similar edit form for filter


        $fn_dg_edit_form = "";
        if ($this->dg_inline == 0) {
            $fn_dg_edit_form = $twig->render('/crud/dg_edit_form.js.twig', array(
                'n' => $this->html_prefix
                , 'input_cell' => $input_cell
                , 'options_obj' => $options_obj
                , 'host_api' => $this->host_api
                , 'api_url' => $url_api_crud
                , 'width_form' => $this->width_form
                , 'height_form' => $this->height_form
            ));
        }

        $fn_dg_filter_form = "";
        if ($this->enable_filter_dg == 1) {
            $fn_dg_filter_form = $twig->render('/crud/dg_filter_form.js.twig', array(
                'n' => $this->html_prefix
                , 'input_cell_filter' => $input_cell_filter
                , 'options_obj_filter' => $options_obj_filter
                , 'width_form' => $this->width_form
                , 'height_form' => $this->height_form
                , 'enable_filter' => $this->enable_filter_dg
            ));
        }

        $js = $twig->render('/crud/index.crud.js.twig', array('n' => $this->html_prefix
            , 'host_api' => $this->host_api
            , 'api_url' => $url_api_crud
            , 'title' => $title
            , 'col_crud' => $this->get_template_js_crud() //this function use after $this->get_api_fn_crud
            , 'pk' => $this->primary_key
            , 'on_after_edit' => $on_after_edit
            , 'pagination' => $pagination
            , 'dg_inline' => $this->dg_inline
            , 'fn_dg_edit_form' => $fn_dg_edit_form
            , 'fn_dg_filter_form' => $fn_dg_filter_form
            , 'filter_base' => $this->filter_base
            , 'ck_row_styler' => $this->ck_row_styler
            , 'row_styler' => $this->row_styler
            , 'group_col' => $this->group_col
            , 'form_full' => $this->form_full
            , 'hide_id_ins' => $hide_id_ins
            , 'crud_c' => $this->crud_c
            , 'crud_r' => $this->crud_r
            , 'crud_u' => $this->crud_u
            , 'crud_d' => $this->crud_d
            , 'e' => ($this->crud_u == 1) ? 'e' : ''
            , 'row_num' => ($this->row_num == 1) ? 'rownumbers: true,' : ''
            , 'enable_filter' => $this->enable_filter_dg
            , 'ck_model_xls' => $this->ck_model_xls
        ));

        $file = $dir . "/js/index.js";
        file_put_contents($file, $js); //write generated html
        //write template api_setup.php
        if ($this->current_driver == "oci") {
            $api_setup = $twig->render('/base/api/api_setup.oci.php.twig', array(
                'cn_name' => $this->oci_name
                , 'oci_cn_var' => $this->oci_cn_var
                , 'oci_user_var' => $this->oci_user_var
                , 'oci_password_var' => $this->oci_password_var
                , 'oci_cn' => $this->oci_cn
                , 'oci_user' => $this->oci_user
                , 'oci_password' => $this->oci_password
                , 'ck_global_var' => $this->ck_global_var
                , 'global_var' => $this->global_var
            ));
        }
        if ($this->current_driver == "odbc") {
            $api_setup = $twig->render('/base/api/api_setup.odbc.php.twig', array(
                'cn_name' => $this->odbc_name
                , 'odbc_cn_var' => $this->odbc_cn_var
                , 'odbc_user_var' => $this->odbc_user_var
                , 'odbc_password_var' => $this->odbc_password_var
                , 'odbc_cn' => $this->odbc_cn
                , 'odbc_user' => $this->odbc_user
                , 'odbc_password' => $this->odbc_password
                , 'ck_global_var' => $this->ck_global_var
                , 'global_var' => $this->global_var
            ));
        }
        if ($this->current_driver == "pdo") {
            $api_setup = $twig->render('/base/api/api_setup.pdo.php.twig', array(
                'cn_name' => $this->pdo_name
                , 'pdo_cn_var' => $this->pdo_cn_var
                , 'pdo_user_var' => $this->pdo_user_var
                , 'pdo_password_var' => $this->pdo_password_var
                , 'pdo_options_var' => $this->pdo_options_var
                , 'pdo_cn' => $this->pdo_cn
                , 'pdo_user' => $this->pdo_user
                , 'pdo_password' => $this->pdo_password
                , 'pdo_options' => $this->pdo_options
                , 'ck_global_var' => $this->ck_global_var
                , 'global_var' => $this->global_var
            ));
        }

        $file = $dir . "/api/api_setup.php";
        file_put_contents($file, $api_setup); //write generated html
        //create api
        $this->union_api_code($dir, $api_url, $fn_api); //create file api
    }

    /** return param javascript for pagination
     * @return string
     */
    private function get_pagination() {
        $tmp = $this->pagination;
        if ($this->pagination == 1) {
            $str = "pagination:true," . PHP_EOL;
            $str .= "pageSize:" . $this->pagination_size . "," . PHP_EOL;
            $str .= "pageList:" . $this->pagination_list . "," . PHP_EOL;
            return $str;
        }
    }

    /** return code es. var combo1 = combo_ABB_COMBO_CRUD()
     *
     * @return type
     */
    private function get_code_var_return_combo() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
        $code_combo = "";

        $model = $this->table_model;
        $i = 0;
        foreach ($model as $value) {
            if ($value["SKIP"] == "0") {
                if ($value["CONSTRAINT_TYPE"] == 'FOREIGN_KEY') {
                    $i += 1;
                    $table = $value["NAME_TABLE_EXT"];
                    $value_field = $value["VALUE_FIELD"];
                    $text_field = $value["TEXT_FIELD"];
                    $col_combo = $value["COL"];
                    $code_combo .= "\$combo$i = combo_$table" . "__" . $col_combo . "();" . PHP_EOL;
                    $code_combo .= "\$data = gii_add_col_combo(\$combo$i, \$data, \"$col_combo\", \"$value_field\", \"$text_field\");" . PHP_EOL;
                }
            }
        }

        return $code_combo;
    }

    /** return all api url code
     *
     * @return type
     */
    private function get_api_name_for_combobox() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
        $api_url = [];
        /*
          $model = $this->table_model;
          foreach ($model as $value) {
          if ($value["SKIP"] == "0") {
          if ($value["CONSTRAINT_TYPE"] == 'FOREIGN_KEY') {
          $table = $value["NAME_TABLE_EXT"];
          $api_url[] = "\$app->post('/combo/$table/:ws', 'combo_$table');";
          }
          }
          }
         */
        return $api_url;
    }

    /** order model by primary key
     *
     * @return type array model
     */
    private function model_order_primary_key() {
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
        return $model_ord;
    }

    /**
     * @return string code javascript for edit form row es <div style="margin-top:5px"><input id="dg1_COMBO">
     */
    private function get_input_cell_for_dg_filter_form() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $model_ord = $this->model_order_primary_key();

        $n = $this->html_prefix;
        $code = "\\n\\" . PHP_EOL;
        foreach ($model_ord as $value) {
            $col = $value["COL"];
            $type = $value["CONSTRAINT_TYPE"];
            if (isset($value["CK_FILTER"])) {
                if (($value["SKIP"] == "0") && ($value["CK_FILTER"] == "1")) {
                    $id_colname = "dg$n" . "_" . $value["COL"];
                    $id_div = "dg$n" . "_div_" . $value["COL"];
                    $colname = $value["COL"];
                    //<input class="easyui-datebox" sharedCalendar="#sc">
                    $dt_beteen = ($this->is_array_gii($value, "CK_FILTER_BETWEEN", 1, 0) == "1") ? "&nbsp;<input id=\"$id_colname" . "__TO\" name=\"$colname" . "__TO\">" : "";
                    $code .= "<div id=\"$id_div\" style=\"margin-top:5px;\"><input id=\"$id_colname\" name=\"$colname\">$dt_beteen</div>\\n\\" . PHP_EOL;
                }
            }
        }
        $code .= "\\n\\";
        return $code;
    }

    /**
     * @return string code javascript for edit form row es <div style="margin-top:5px"><input id="dg1_COMBO">
     */
    private function get_input_cell_for_dg_edit_form() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $model_ord = $this->model_order_primary_key();

        $n = $this->html_prefix;
        $code = "\\n\\" . PHP_EOL;
        foreach ($model_ord as $value) {
            $col = $value["COL"];
            $type = $value["CONSTRAINT_TYPE"];
            if ($value["SKIP"] == "0") {
                $hide_form = false;
                if (isset($value["HIDE_FORM"])) {
                    $hide_form = ($value["HIDE_FORM"] == 1) ? true : false;
                }
                $hide = ($hide_form) ? "display:none;" : "";
                $id_colname = "dg$n" . "_" . $value["COL"];
                $id_div = "dg$n" . "_div_" . $value["COL"];
                $colname = $value["COL"];
                $code .= "<div id=\"$id_div\" style=\"margin-top:5px;$hide\"><input id=\"$id_colname\" name=\"$colname\"></div>\\n\\" . PHP_EOL;
            }
        }
        $code .= "\\n\\";
        return $code;
    }

    /**
     * @return string code javascript for hide id for edit form row es. $('#dg1_NOMINATIVI_PROFILI').hide();
     */
    private function get_id_hide_for_dg_edit_form() {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $n = $this->html_prefix;
            $id_all = "";
            $i = 0;
            $model = $this->table_model;
            foreach ($model as $value) {
                $col = $value["COL"];
                $hide_ins = (!isset($value["HIDE_INS"])) ? 0 : $value["HIDE_INS"];
                if ($hide_ins == 1) {
                    $i += 1;
                    $dot = ($i > 1) ? ", " : "";
                    $id_div = $dot . "#dg$n" . "_div_" . $value["COL"];
                    $id_all = $id_all . $id_div;
                }
            }
            return ($i > 0) ? "$('$id_all').hide();" : "";
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** cretate code for crud type column
     *
     * @return string es. "columns: [[ {field: 'ck', checkbox: true}, {field: 'ID', title: 'ID', width: '30px'
     */
    private function get_template_js_crud() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $model_ord = $this->model_order_primary_key();

        if ($this->lock_col == "") {
            $code = "";
            foreach ($model_ord as $value) {
                $col = $value["COL"];
                $type = $value["CONSTRAINT_TYPE"];
                if ($value["SKIP"] == "0") {
                    $code .= $this->get_js_inline_crud_col($value);
                }
            }
            $code = "columns: [[" . PHP_EOL . $code . PHP_EOL . "]]," . PHP_EOL;
            return $code;
        } else { // for frozen col, is lock scrolling col
            $code = "";
            $code_frozen = "";
            $is_frozen = true;
            foreach ($model_ord as $value) {
                $col = $value["COL"];
                $type = $value["CONSTRAINT_TYPE"];
                if ($value["SKIP"] == "0") {
                    ($is_frozen) ? $code_frozen .= $this->get_js_inline_crud_col($value) : $code .= $this->get_js_inline_crud_col($value);
                    ($col == $this->lock_col) ? $is_frozen = false : false;
                }
            }
            $code_frozen = "frozenColumns: [[" . PHP_EOL . $code_frozen . PHP_EOL . "]]," . PHP_EOL;
            $code = "columns: [[" . PHP_EOL . $code . PHP_EOL . "]]," . PHP_EOL;
            return $code_frozen . $code;
        }
    }

    /** set if enable advanced filter
     * @throws Exception
     */
    private function set_enable_filter_dg() {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
            $enable_filter = 0;
            $model = $this->table_model;
            foreach ($model as $value) {
                if (isset($value["CK_FILTER"])) {
                    if ($value["CK_FILTER"] == 1) {
                        $enable_filter = 1;
                    }
                }
            }
            $this->enable_filter_dg = $enable_filter;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /**
     *
     * @return string return cord option of object
     */
    private function get_option_for_dg_filter_form() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
        $code = "";
        $model_ord = $this->model_order_primary_key();
        foreach ($model_ord as $value) {
            if (isset($value["CK_FILTER"])) {
                if (($value["SKIP"] == "0") && ($value["CK_FILTER"] == "1")) {
                    $value["READONLY"] = 0; //
                    ($value["TYPE"] == "textarea") ? $value["TYPE"] = "textbox" : false;
                    $code .= $this->get_option_for_field_form($value, true); //for filter
                    if ($this->is_array_gii($value, "CK_FILTER_BETWEEN", 1, 0) == "1") {
                        //add second date
                        $value["COL"] = $value["COL"] . "__TO";
                        $value["TITLE"] = $this->T("intervallo date");
                        $code .= $this->get_option_for_field_form($value, true); //for filter
                    }
                }
            }
        }
        return $code;
    }

    /**
     *
     * @return string return cord option of object
     */
    private function get_option_for_dg_edit_form() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
        $code = "";
        $model_ord = $this->model_order_primary_key();
        foreach ($model_ord as $value) {
            $col = $value["COL"];
            $type = $value["CONSTRAINT_TYPE"];
            if ($value["SKIP"] == "0") {
                $code .= $this->get_option_for_field_form($value, false); //no filter
            }
        }
        return $code;
    }

    /**
     *
     * @param type $row
     * @param type $hide
     * @param tyle $filer true for option filter
     * @return type
     */
    private function get_option_for_field_form($row, $filter) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $col = $row["COL"];
        $title = $row["TITLE"];
        $type = $row["TYPE"];
        $width_field = $row["WIDTH_FORM"];
        $width_label = $row["WIDTH_LABEL"];

        $label_align = isset($row["LABEL_ALIGN"]) ? $row["LABEL_ALIGN"] : "";
        $label_align = (($label_align == "") || ($label_align == "left")) ? "" : "labelAlign:'$label_align',";
        $label_position = isset($row["LABEL_POSITION"]) ? $row["LABEL_POSITION"] : "";
        $label_position = (($label_position == "") || ($label_position == "before")) ? "" : "labelPosition:'$label_position',";
        $type_pk_fk = $row["CONSTRAINT_TYPE"];
        $readonly = isset($row["READONLY"]) ? $row["READONLY"] : 0;
        $readonly = ($readonly == 1) ? "readonly:true," : "";
        $editable = ($row["EDIT"] == 0) ? "editable:false," : "";
        $sortable = ($row["SORTABLE"] == 1) ? "sortable: true," : "sortable: false,";
        $required = ($row["REQUIRED"] == 1) ? "required: true," : "required: false,";

        $table_ext = $row["NAME_TABLE_EXT"]; //table external for combobox
        $value_field = $row["VALUE_FIELD"]; // value for combobox
        $text_field = $row["TEXT_FIELD"];   // text for combobox
        $ck_limit2list = (isset($row["CK_LIMIT2LIST"])) ? $row["CK_LIMIT2LIST"] : "";
        $limit2list = (($ck_limit2list == "1") || ($ck_limit2list == "")) ? "limitToList: true," : "";

        $panel_width = isset($row["PANEL_WIDTH"]) ? $row["PANEL_WIDTH"] : "";
        $panel_width = (($panel_width == "") || ($panel_width == "250px")) ? "" : "panelWidth:'$panel_width',";

        $url_combobox = "api/data/combo_$table_ext" . "__" . $col . ".json"; //url api combobox
        $n_dg = $this->html_prefix;
        $n_row = $row["N_ROW_TEXTAREA"];
        ($n_row == "") ? $n_row = 2 : false;
        $height_area = round(15 * $n_row) + 10;
        ($n_row == 1) ? $height_area == 24 : false;
        ($n_row == 2) ? $height_area == 40 : false;
        $list = $row["LIST"];
        $cat = isset($row["LIST_CAT"]) ? $row["LIST_CAT"] : "";
        $cat = ($cat == "1") ? "groupField: 'cat'," : "";
        $icon = isset($row["LIST_ICON"]) ? $row["LIST_ICON"] : "";
        $icon = ($icon == "1") ? "showItemIcon:true," : "";
        $fields = (isset($row["FIELDS"])) ? $row["FIELDS"] : "";
        $columns = ($fields != '') ? $this->get_fields_for_combogrid($fields) : "";
        // for bind field on select
        $on_select_combogrid = ($fields != "") ? $this->get_for_combogrid__selecet_on($fields) : "";
        $dt_bt_text = $this->app_setting["testo pulsante data massima per datebox"];
        $dt_value_max = $this->app_setting["valore data massima per datebox"];
        $buttons_dt = ($this->is_array_gii($row, "CK_DT_MAX", 1, 0) == 1) ? "buttons: add_bt2datebox('$dt_bt_text','$dt_value_max')," : "";

        $id_object = "$('#dg$n_dg" . "_$col').";

        $pk = $this->primary_key;

        $width = "";
        $colt = $this->T($title); //translate
        $label_with = ($width_label != "") ? "labelWidth:'$width_label'," : "";
        $label = "label:'$colt',$label_with";
        if ($width_field == "") {
            //$px = (strlen($colt) * 10) . "px";
            //$width = "width: '$px',";
        } else {
            $width = "width: '$width_field',";
        }

        //for filter
        $multiple = "";
        if ($filter) {
            $required = "";
            if (isset($row["CK_FILTER_REQUIRED"])) {
                $required = ($row["CK_FILTER_REQUIRED"] == 1) ? "required: true," : "required: false,";
            }
            if (isset($row["CK_FILTER_MULTIPLE"])) {
                $multiple = ($row["CK_FILTER_MULTIPLE"] == 1) ? "multiple: true," : "multiple: false,";
            }


            //for last
            ($row["CK"] == 1) ? $multiple = "multiple: true," : false;
        }

        if ($type_pk_fk == "PRIMARY_KEY") {
            $editor = "$id_object" . "textbox({" . PHP_EOL . "editable:false, $width $label $label_align $label_position $required });" . PHP_EOL;
            $editor = str_replace(",", "," . PHP_EOL, $editor);
            return $editor;
        }

        if ($row["CK"] == "1") {
            $yes = $this->T("si");
            $no = $this->T("no");
            $data = "[{text: '$yes',value:'1'},{text:'$no',value:'0'}]";
            $editor = "$id_object" . "combobox({" . PHP_EOL . "valueField: 'value', textField: 'text', $width $label $label_align $label_position $required panelHeight:50, data:$data, limitToList: true, $readonly $multiple });" . PHP_EOL;
            $editor = str_replace(", ", "," . PHP_EOL, $editor); //only space return dot
            return $editor;
        }


        if ($type == "textbox") {
            $editor = "$id_object" . "textbox({" . PHP_EOL . " $width $label $label_align $label_position $required  $readonly});" . PHP_EOL;
            $editor = str_replace(",", "," . PHP_EOL, $editor);
            return $editor;
        }
        if ($type == "textarea") {
            $editor = "$id_object" . "textbox({" . PHP_EOL . "multiline:true,height:$height_area, $width $label $label_align $label_position $required $readonly });" . PHP_EOL;
            $editor = str_replace(",", "," . PHP_EOL, $editor);
            return $editor;
        }

        //escludo the column primary key for edit
        if ($type == "numberbox") {
            $editor = "$id_object" . "numberbox({" . PHP_EOL . " $width $label $label_align $label_position $required $readonly});" . PHP_EOL;
            $editor = str_replace(",", "," . PHP_EOL, $editor);
            return $editor;
        }

        if ($type == "datebox") {
            $with = "width: '100px',";
            ($this->date_format = "DD-MM-YYYY") ? $type_dt = "it" : $type_dt = "en";
            $date_format = "formatter: myformatter_d_$type_dt, parser: myparser_d_$type_dt,";
            $editor = "$id_object" . "datebox({" . PHP_EOL . " $width $label $label_align $label_position $required $readonly $buttons_dt $date_format});" . PHP_EOL;
            $editor = str_replace(", ", "," . PHP_EOL, $editor);
            return $editor;
        }
        if ($type == "combobox") {
            if ($type_pk_fk == "FOREIGN_KEY") {
                $editor = "$id_object" . "combobox({" . PHP_EOL . "$width $label $label_align $label_position valueField: '$value_field',textField: '$text_field', method: 'get',url: '$url_combobox',$required $panel_width $limit2list $readonly $multiple});" . PHP_EOL;

                $editor = str_replace(",", "," . PHP_EOL, $editor);
                return $editor;
            }
            if ($type_pk_fk == "LIST") {
                $editor = "$id_object" . "combobox({" . PHP_EOL . "$width $label $label_align $label_position valueField: '$value_field',textField: '$text_field', $icon $cat data:$list, $required panelWidth: 250, $limit2list $readonly});" . PHP_EOL;
                $editor = str_replace(", ", "," . PHP_EOL, $editor);
                return $editor;
            }
        }
        if ($type == "combogrid") {
            if ($type_pk_fk == "FOREIGN_KEY") {
                $filter = PHP_EOL . "$id_object combogrid('grid').datagrid('enableFilter');";
                $editor = "$id_object" . "combogrid({" . PHP_EOL . "$width $label $label_align $label_position valueField: '$value_field', textField: '$text_field', idField: '$value_field', method: 'get', url: '$url_combobox', $required $panel_width $readonly $limit2list $readonly reserved:true, $on_select_combogrid $multiple #columns});$filter" . PHP_EOL;
                $editor = str_replace(", ", "," . PHP_EOL, $editor);
                $editor = str_replace("#columns", $columns, $editor);
                return $editor;
            }
        }
    }

    /** return string code js for columns grid
     *
     *  es. {field: 'SEQ', title: 'Ord', width: '25px', editor: {type: 'numberbox', options: {required: true}}, sortable: true},
     *
     * @param type $col string name column
     */
    private function get_js_inline_crud_col($row) {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $col = $row["COL"];
            $title = $row["TITLE"];
            $type = isset($row["TYPE"]) ? $row["TYPE"] : "textbox";
            $width_field = $row["WIDTH"];
            $type_pk_fk = isset($row["CONSTRAINT_TYPE"]) ? $row["CONSTRAINT_TYPE"] : "";

            $readonly = isset($row["READONLY"]) ? $row["READONLY"] : 0;
            $edit = ($this->dg_inline == 1) ? !$readonly : "0";
            $sortable = ($this->is_array_gii($row, "SORTABLE", 1, 0) == 1) ? "sortable: true," : "sortable: false,";
            $required = ($this->is_array_gii($row, "REQUIRED", 1, 0) == 1) ? "required: true," : "required: false,";
            $table_ext = isset($row["NAME_TABLE_EXT"]) ? $row["NAME_TABLE_EXT"] : ""; //table external for combobox
            $value_field = isset($row["VALUE_FIELD"]) ? $row["VALUE_FIELD"] : ""; // value for combobox
            $text_field = isset($row["TEXT_FIELD"]) ? $row["TEXT_FIELD"] : "";   // text for combobox
            $ck_limit2list = (isset($row["CK_LIMIT2LIST"])) ? $row["CK_LIMIT2LIST"] : "";
            $limit2list = (($ck_limit2list == "1") || ($ck_limit2list == "")) ? "limitToList: true," : "";
            $limit2list_combogrid = (($ck_limit2list == "1") || ($ck_limit2list == "")) ? "editable: false," : "";
            $url_combobox = "api/data/combo_$table_ext" . "__" . $col . ".json"; //url api combobox
            $n_dg = $this->html_prefix;
            $hiden = ($row["HIDE"] == "1") ? "hidden:true," : "";
            $list = isset($row["LIST"]) ? $row["LIST"] : "";
            $cat = isset($row["LIST_CAT"]) ? $row["LIST_CAT"] : "";
            $cat = ($cat == "1") ? "groupField: 'cat'," : "";
            $icon = isset($row["LIST_ICON"]) ? $row["LIST_ICON"] : "";
            $icon = ($icon == "1") ? "showItemIcon:true," : "";
            $fields = (isset($row["FIELDS"])) ? $row["FIELDS"] : "";
            $columns = ($fields != '') ? $this->get_fields_for_combogrid($fields) : "";
            $dt_bt_text = $this->app_setting["testo pulsante data massima per datebox"];
            $dt_value_max = $this->app_setting["valore data massima per datebox"];
            $buttons_dt = ($this->is_array_gii($row, "CK_DT_MAX", 1, 0) == 1) ? "buttons: add_bt2datebox('$dt_bt_text','$dt_value_max')," : "";

            $pk = $this->primary_key;

            $width = "";
            $colt = $this->T($title); //translate
            if ($width_field == "") {
                //$px = (strlen($colt) * 10) . "px";
                //$width = "width: '$px',";
            } else {
                $width = "width: '$width_field',";
            }

            if ($type_pk_fk == "PRIMARY_KEY") {
                $ck = "{field: 'ck', checkbox: true}," . PHP_EOL;
                return $ck . "{field: '$col', title: '$colt', $width $sortable $hiden}," . PHP_EOL;
            }

            if ($this->is_array_gii($row, "CK", 1, 0) == 1) {
                $editor = ($edit == "1") ? "editor: {type: 'checkbox', options: {on: '1', off: '0'}}," : "";
                return "{field: '$col', title: '$colt', $editor formatter: mycheck, $required $sortable $hiden}," . PHP_EOL;
            }


            if ($type == "textbox") {
                $editor = ($edit == "1") ? "editor: {type: 'textbox', options: { $required }}," : "";
                return "{field: '$col', title: '$colt', $width  $editor $sortable $hiden}," . PHP_EOL;
            }
            if ($type == "textarea") {
                $editor = ($edit == "1") ? "editor: {type: 'textarea', options: { $required } $hiden}," : "";
                return "{field: '$col', title: '$colt', $width  $editor $sortable}," . PHP_EOL;
            }

            //escludo the column primary key for edit
            if ($type == "numberbox") {
                $with = "width: '50px',";
                $editor = ($edit == "1") ? "editor: {type: 'numberbox', options: { $required }}," : "";
                return "{field: '$col', title: '$colt', $width $editor $sortable $hiden}," . PHP_EOL;
            }

            if ($type == "datebox") {
                $with = "width: '100px',";
                ($this->date_format = "DD-MM-YYYY") ? $type_dt = "it" : $type_dt = "en";
                $date_format = "formatter: myformatter_d_$type_dt, parser: myparser_d_$type_dt,";
                $editor = ($edit == "1") ? "editor: {type: 'datebox', options: { $date_format $required $buttons_dt}}," : "";
                return "{field: '$col', title: '$colt', $width $editor $sortable $hiden}," . PHP_EOL;
            }
            if ($type == "combogrid") {
                if ($type_pk_fk == "FOREIGN_KEY") {
                    $on_select = "onSelect: function (index,row) {
                                var row_dg = $('#dg$n_dg').datagrid('getRows')[g_edit_index];
                                row_dg['$col" . "__TEXT'] = row.$text_field
                            },";
                    $formatter = PHP_EOL . "formatter: function (value, row, index)" . PHP_EOL . " {return row.$col" . "__TEXT;}," . PHP_EOL;
                    $editor = "editor: {type: 'combogrid', options: {" . PHP_EOL . "valueField: '$value_field', textField: '$text_field', idField: '$value_field', method: 'get', url: '$url_combobox', $required panelWidth: 250, $limit2list_combogrid $on_select #columns}},";
                    $editor = str_replace(", ", "," . PHP_EOL, $editor);
                    $editor = str_replace("#columns", $columns, $editor);
                    $editor = ($edit == "1") ? $editor : "";
                    return "{field: '$col', title: '$colt', $width $formatter $editor $sortable $hiden}," . PHP_EOL;
                }
            }

            if ($type == "combobox") {
                if ($type_pk_fk == "FOREIGN_KEY") {
                    $on_select = "onSelect: function (record) {
                                var index = $(this).closest('tr.datagrid-row').attr('datagrid-row-index');
                                var row = $('#dg$n_dg').datagrid('getRows')[index];
                                row['$col" . "__TEXT'] = record.$text_field
                            },";
                    $formatter = PHP_EOL . "formatter: function (value, row, index)" . PHP_EOL . " {return row.$col" . "__TEXT;}," . PHP_EOL;
                    $editor = "editor: {type: 'combobox', options: {" . PHP_EOL . "valueField: '$value_field',textField: '$text_field',method: 'get',url: '$url_combobox',$required panelWidth: 250, $limit2list $on_select}},";
                    $editor = str_replace(",", "," . PHP_EOL, $editor);
                    $editor = ($edit == "1") ? $editor : "";
                    return "{field: '$col', title: '$colt', $width $formatter $editor $sortable $hiden}," . PHP_EOL;
                }
                if ($type_pk_fk == "LIST") {
                    $on_select = "onSelect: function (record) {
                                    var index = $(this).closest('tr.datagrid-row').attr('datagrid-row-index');
                                    var row = $('#dg$n_dg').datagrid('getRows')[index];
                                    row['$col" . "__TEXT'] = record.$text_field
                              },";
                    $formatter = PHP_EOL . "formatter: function (value, row, index){
                                    var data = $list;
                                    for (var i = 0; i < data.length; i++) {
                                        if (value == data[i].$value_field) {
                                            return data[i].$text_field;
                                        }
                                    }
                                    return value;
                                },";
                    $editor = "editor: {type: 'combobox', options: {" . PHP_EOL . "valueField: '$value_field', textField: '$text_field', data:$list, $cat $icon $required panelWidth: 250, $limit2list $on_select}},";
                    $editor = str_replace(", ", "," . PHP_EOL, $editor);
                    $editor = ($edit == "1") ? $editor : "";
                    return "{field: '$col', title: '$colt', $width $formatter $editor $sortable $hiden}," . PHP_EOL;
                }
            }

            $editor = ($edit == "1") ? "editor: {type: '??$type??', options: { $required}}," : "";
            return "{field: '$col', title: '$colt', $width $editor $sortable $hiden}," . PHP_EOL;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** from modele dg , from name FIELD get type es DESCART -> textbox
     *
     * @return string
     * @throws Exception
     */
    private function get_type_field_from_model($find) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
        $model = $this->table_model;
        try {
            foreach ($model as $value) {
                if ($value["COL"] == $find) {
                    return ($value["TYPE"] == "textarea") ? "textbox" : $value["TYPE"];
                }
            }
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** generate "onSelect" code for combogrid for bind fields
     * 
     * @param type $model rows combobox
     * @return string
     * @throws Exception
     */
    private function get_for_combogrid__selecet_on($model) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
        try {

            $model_ord = json_decode($model, true);

            //$model_ord = $this->model_order_primary_key();
            $code = "";
            foreach ($model_ord as $value) {
                $col = $value["COL"];
                if (isset($value["BINDFIELD"])) {
                    if (($value["SKIP"] == "0") && ($value["BINDFIELD"] != "")) {
                        $field_bind = "dg" . $this->html_prefix . "_" . $value["BINDFIELD"];
                        $type = $this->get_type_field_from_model($value["BINDFIELD"]);
                        $code .= "$('#$field_bind').$type('setValue',row.$col);";
                    }
                }
            }

            $code = ($code != "") ? "onSelect: function(index,row){" . PHP_EOL . $code . PHP_EOL . "}," . PHP_EOL : "";
            return $code;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    private function get_fields_for_combogrid($model) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
        try {

            $model_ord = json_decode($model, true);

            //$model_ord = $this->model_order_primary_key();
            $code = "";
            foreach ($model_ord as $value) {
                $col = $value["COL"];
                $type = "textbox";
                if ($value["SKIP"] == "0") {
                    $hide = ($value["HIDE"] == "1") ? true : false;
                    $value['EDIT'] = 0;
                    $code .= $this->get_js_inline_crud_col($value, $hide);
                }
            }
            $code = "columns: [[" . PHP_EOL . $code . PHP_EOL . "]]," . PHP_EOL;
            return $code;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /**  create sql string
     *
     * @param type $table
     * @param type $model
     * @return type
     * @throws Exception
     */
    public function get_sql_for_select($table, $model) {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $app = Slim\Slim::getInstance();

            $str_col_w_a = "";
            $str_col = "";
            $ncol = 0;
            //$model = $this->table_model;
            foreach ($model as $value) {
                $col_name = $value["COL"];
                $alias = $this->sql_alias; // "A";
                $alias_str = ($this->ck_sql_alias == "1") ? "$alias." : "";
                $col_name_w_a = $alias_str . $col_name;
                $col_type = $value["TYPE"];
                //skip cols
                if ($value["SKIP"] == "0") {
                    $ncol += 1;
                    if ($col_type == "datebox") {
                        $col_name_w_a = $this->format_date_to_char($col_name_w_a, $col_name);
                    }
                    $strComma = ($ncol > 1) ? ", " : "";
                    $str_col_w_a .= $strComma . $col_name_w_a; //list col with alias
                    $str_col .= $strComma . $col_name; //list col without alias
                }
            }
            $str_col_alias = ($this->ck_sql_alias == "1") ? $alias : "";
            $strSql = "SELECT $str_col_w_a FROM $table $str_col_alias";
            return $strSql;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** get fileds for intestation excel
     *  @throws Exception
     */
    public function get_field_intestation_for_xls() {
        try {
            $ar_xls = [];
            $model = $this->model_xls;
            foreach ($model as $value) {
                if ($value["SKIP"] == "0") {
                    $ar_xls[] = "\"" . $value["COL"] . "\"=>\"" . $value["TITLE"] . "\"";
                }
            }
            return implode(",", $ar_xls);
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** get field from excel
     *
     * @throws Exception
     */
    public function get_field_from_model_xls() {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $model = $this->get_field_from_model();
            $new_model = [];
            foreach ($model as $value) {
                //CONSTRAINT_TYPE]:FOREIGN_KEY
                if (in_array($value["TYPE"], ["numberbox", "combobox", "combogrid"])) {
                    $tst = strpos($value["TYPE"], "__TEXT");
                    (strpos($value["COL"], "__TEXT") > 0) ? $value['TYPE'] = "text" : $value['TYPE'] = "number";
                    $new_model[] = $value;
                } else {
                    $value['TYPE'] = "text";
                    $new_model[] = $value;
                }
            }
            return $new_model;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** get field from model and add hide field generated from combo
     *
     * @throws Exception
     */
    public function get_field_from_model() {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $model = $this->table_model;
            $new_model = [];
            foreach ($model as $value) {
                //CONSTRAINT_TYPE]:FOREIGN_KEY
                if ($value["CONSTRAINT_TYPE"] == "FOREIGN_KEY") {
                    $add_value = $value;
                    $add_value["COL"] = $value["COL"] . "__TEXT";
                    $new_model[] = $value; //duplicate record
                    $new_model[] = $add_value;
                } else {
                    $new_model[] = $value;
                }
            }
            return $new_model;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** get table model from db
     *
     * @return type
     * @throws Exception
     */
    public function get_table_model_from_db($table) {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


            $app = Slim\Slim::getInstance();
            //$table = $this->table_name;
            if ($this->current_driver == "oci") {
                $sql = "
                        WITH COL_CONSTRAINT AS (
                                        SELECT
                                        C.TABLE_NAME
                                        ,C.COLUMN_NAME
                                        ,CASE D.CONSTRAINT_TYPE WHEN 'P' THEN 'PRIMARY_KEY' WHEN 'R' THEN 'FOREIGN_KEY' ELSE  D.CONSTRAINT_TYPE END CONSTRAINT_TYPE
                                        --PK TABLE EXTERNAL
                                         ,(
                                             SELECT F.TABLE_NAME FROM ALL_CONS_COLUMNS F WHERE OWNER=USER
                                             AND F.OWNER=USER AND F.CONSTRAINT_NAME=D.R_CONSTRAINT_NAME
                                         ) NAME_TABLE_EXT
                                         ,(
                                                         SELECT F.COLUMN_NAME FROM ALL_CONS_COLUMNS F WHERE OWNER=USER
                                                         AND F.OWNER=USER AND F.CONSTRAINT_NAME=D.R_CONSTRAINT_NAME
                                          ) PK_TABLE_EXT
                                        FROM

                                        ALL_CONSTRAINTS D INNER JOIN ALL_CONS_COLUMNS C
                                        ON  (
                                                D.CONSTRAINT_NAME =C.CONSTRAINT_NAME AND D.OWNER =C.OWNER AND C.OWNER=USER AND  C.TABLE_NAME='$table'
                                                AND D.CONSTRAINT_TYPE IN ('P','R')
                                        )
                        )
                        SELECT
                                         A.COLUMN_NAME COL
                                         ,A.COLUMN_NAME TITLE
                                         ,case when B.CONSTRAINT_TYPE='FOREIGN_KEY' then 'combobox' else
                                                 case A.DATA_TYPE when 'NUMBER' then  'numberbox'
                                                                   when 'VARCHAR' then  'textbox'
                                                                   when 'VARCHAR2' then 'textbox'
                                                                   when 'DATE' then 'datebox'
                                                                   else A.DATA_TYPE
                                                end
                                        end TYPE
                                    , NVL(B.CONSTRAINT_TYPE,' ') CONSTRAINT_TYPE
                                    , 0 SKIP
                                    , case when B.CONSTRAINT_TYPE='PRIMARY_KEY' then 1 else 0 end  HIDE
                                    , 0 CK
                                    , 1 EDIT
                                    , case A.NULLABLE when 'Y' then 1 when 'N' then 0 end REQUIRED
                                    , 1 SORTABLE
                                    ,'' WIDTH
                                    ,'' WIDTH_FORM
                                    ,'' WIDTH_LABEL
                                    ,B.NAME_TABLE_EXT
                                    ,B.PK_TABLE_EXT value_field

                                    ,( select G.COLUMN_NAME
                                       from ALL_TAB_COLUMNS G where G.owner=user
                                       and G.TABLE_NAME=B.NAME_TABLE_EXT
                                       and G.COLUMN_NAME<>(select h.pk_table_ext from COL_CONSTRAINT h where h.name_table_ext=B.NAME_TABLE_EXT and rownum=1)
                                       and rownum=1
                                    ) text_field
                                    ,'' N_ROW_TEXTAREA
                                    , '' LIST
                                    , '' LIST_CAT
                                    , '' LIST_ICON

                        FROM ALL_TAB_COLUMNS A
                        LEFT JOIN COL_CONSTRAINT B ON ( A.COLUMN_NAME=B.COLUMN_NAME)
                        WHERE A.TABLE_NAME='$table' AND A.OWNER=USER
                        ORDER BY A.COLUMN_ID
                        ";
                error_log(LogTime() . ' Sql, get model table: ' . PHP_EOL . $sql . PHP_EOL, 3, 'logs/sql.log');


                //$conn = oci_connect($db4_user, $db4_psw, $db4_GOLD, 'UTF8');
                $conn = oci_connect($this->oci_user, $this->oci_password, $this->oci_cn, $this->oci_charset);
                $db = oci_parse($conn, $sql);
                $rs = oci_execute($db);

                oci_fetch_all($db, $data, null, null, OCI_ASSOC + OCI_FETCHSTATEMENT_BY_ROW);
                $data_r = $this->set_title_model_from_ar_setting($data);
                $data_r2 = $this->set_flag_onoff_model_from_ar_setting($data_r);
                $data_r3 = $this->set_flag_hide_model_from_ar_setting($data_r2);
                $data_r4 = $this->set_width_for_field_form_crud($data_r3);
                return $data_r4;
            }
            if (($this->current_driver == "pdo") && ($this->type_db == "mysql")) {

                $sql = "
                    SELECT
                        A.COLUMN_NAME COL
                        ,A.COLUMN_NAME TILTE
                        ,case A.DATA_TYPE when 'INT' then  'numberbox'
                                          when 'VARCHAR' then  'textbox'
                                          when 'DATE' then 'datebox'
                                          else A.DATA_TYPE END TYPE
                        ,(
                        SELECT
                          CASE WHEN B.REFERENCED_TABLE_NAME IS NOT NULL THEN \"FOREIGN_KEY\" ELSE \"PRIMARY_KEY\" end CONSTRAINT_TYPE
                        FROM
                            INFORMATION_SCHEMA.KEY_COLUMN_USAGE B
                        WHERE 1=1
                            AND B.TABLE_SCHEMA = DATABASE()
                                AND B.TABLE_NAME = '$table'
                                AND B.COLUMN_NAME=A.COLUMN_NAME
                        ) CONSTRAINT_TYPE
                        ,CASE WHEN IS_NULLABLE=\"NO\" THEN 1 ELSE 0 END REQUIRED
                        #COLUMN_COMMENT
                        , 0 SKIP
                        FROM INFORMATION_SCHEMA.COLUMNS A
                        WHERE A.TABLE_SCHEMA = DATABASE() AND A.TABLE_NAME = '$table'
                        
                         ";
                $dbh = new PDO($this->pdo_cn, $this->pdo_user, $this->pdo_password, $this->pdo_options_gii);
                $sth = $dbh->prepare($sql);
                $sth->execute();
                $data = $sth->fetchAll();
                $data_r = $this->set_title_model_from_ar_setting($data);
                $data_r2 = $this->set_flag_onoff_model_from_ar_setting($data_r);
                $data_r3 = $this->set_flag_hide_model_from_ar_setting($data_r2);
                $data_r4 = $this->set_width_for_field_form_crud($data_r3);
                return $data_r4;
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
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $model = $this->table_model;
            foreach ($model as $value) {
                if ($value["CONSTRAINT_TYPE"] == "PRIMARY_KEY") {
                    return $value["COL"];
                }
            }
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** get primary key
     *
     * @return type string primary key
     */
    private function get_primary_key() {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
            if ($this->current_driver == "oci") {
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
            }
            if (($this->current_driver == "pdo") && ($this->type_db == "mysql")) {
                $app = Slim\Slim::getInstance();
                $table = $this->table_name;
                $sql = "
                    SELECT COLUMN_NAME
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                       AND TABLE_NAME = '$table'
                       AND COLUMN_KEY = 'PRI';
                ";

                error_log(LogTime() . ' Sql, get primary key: ' . PHP_EOL . $sql . PHP_EOL, 3, 'logs/sql.log');

                $dbh = new PDO($this->pdo_cn, $this->pdo_user, $this->pdo_password, $this->pdo_options_gii);
                $sth = $dbh->prepare($sql);
                $sth->execute();
                $data = $sth->fetchAll();

                if (Count($data) > 0) {
                    $pk = $data[0]['COLUMN_NAME'];
                    return $pk;
                } else {
                    // error not find primary key
                    throw new Exception($this->T('Errore - non è possibile recuperare la primary key'));
                }
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

    /** check if valute is in to arary and return $default if not present  es $value['aaa']
     * is_array_gii(is_array_gii($value,'ID', 1, 0)  ->  1 if present or 0 if not present
     * @param type $ar
     * @param type $property
     * @param type $find
     * @param type $default
     * @return type
     * @throws Exception
     */
    private function is_array_gii($ar, $property, $find, $default) {
        try {
            if (isset($ar)) {
                if (isset($ar[$property])) {
                    return ($ar[$property] == $find) ? $find : $default;
                } else {
                    return $default;
                }
            } else {
                return $default;
            }
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** add filter query to sql
     *
     * @param type $sql string sql
     * @return type
     * @throws Exception
     */
    private function set_filter2sql($sql) {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $model = $this->table_model;
            $str_filter = "";
            $var_filter = "";
            $var_filter_assign = "";
            foreach ($model as $value) {
                if ($this->is_array_gii($value, "CK_FILTER", 1, 0) == "1") {
                    //if ((isset($value["CK_FILTER"])) && ($value["CK_FILTER"] == 1)) {
                    $col = $value["COL"];
                    $str_condition = "\"\"";

                    $var_filter .= "\$str_filter_$col" . PHP_EOL;
                    //assign parameter
                    $multiple = false;
                    ($value["CK"] == "1") ? $multiple = true : false;
                    ($value["TYPE"] == "combogrid") ? $multiple = true : false;
                    ($value["TYPE"] == "combobox") ? $multiple = true : false;


                    //((isset($filter)) && $filter["CAMPO1"])
                    if (!$multiple) {
                        $var_filter_assign .= "\$filter_$col = (isset(\$filter[\"$col\"])) ? \$filter[\"$col\"] : \"\";" . PHP_EOL; // assign parameter
                        $var_filter_assign .= ($this->is_array_gii($value, "CK_FILTER_BETWEEN", 1, 0) == "1") ? "\$filter_$col" . "__TO = ((isset(\$filter)) && isset(\$filter[\"$col" . "__TO\"])) ? \$filter[\"$col" . "__TO\"] : \"\";" . PHP_EOL : ""; // assign 2°  for date between
                    } else {
                        //$filter_COMBO
                        $var_filter_assign .= "\$filter_$col = (isset(\$filter[\"$col\"])) ? \$filter[\"$col\"] : \"\";" . PHP_EOL; // assign parameter
                        $var_filter_assign .= "\$filter_$col = (is_array(\$filter_$col)) ? implode(\",\", \$filter_$col) : \$filter_$col;" . PHP_EOL;
                    }
                    if (($value["TYPE"] == "textbox") || ($value["TYPE"] == "textarea")) {
                        if ($value["CK_FILTER_LIKE"] == 1) {
                            $str_condition = "\"AND $col LIKE '%\$filter_$col%'\"";
                        } else {
                            $str_condition = "\"AND $col = '\$filter_$col'\"";
                        }
                    }
                    // for number box
                    if (($value["TYPE"] == "numberbox") && ($value["CK"] == "0")) {
                        $str_condition = "\"AND $col = \$filter_$col\"";
                    }
                    // for field yes/no
                    if (($value["TYPE"] == "numberbox") && ($value["CK"] == "1")) {
                        $str_condition = "\"AND $col in (\$filter_$col)\"";
                    }

                    if (($value["TYPE"] == "combobox") || ($value["TYPE"] == "combogrid")) {
                        $str_condition = "\"AND $col in (\$filter_$col)\"";
                        // for id cpmbobox in text
                        $quote = "";
                        if (isset($value["CK_FILTER_IDTEXT"])) {
                            $var_filter_assign .= "// for quote on combo" . PHP_EOL;
                            $var_filter_assign .= "\$filter_$col = (\$filter_$col != \"\") ? \"'\" . str_replace(\",\", \"','\", \$filter_$col) . \"'\" : \"\";" . PHP_EOL . PHP_EOL;
                        }
                    }

                    if ($value["TYPE"] == "datebox") {
                        if ($this->is_array_gii($value, "CK_FILTER_BETWEEN", 1, 0) == "1") {
                            $dt = $this->format_dt2todate($col);
                            $dt_from = $this->format_dt2todate("'\$filter_" . $col . "'");
                            $dt_to = $this->format_dt2todate("'\$filter_" . $col . "__TO'");
                            $str_condition = "\"AND $dt BETWEEN $dt_from and $dt_to\"";
                        } else {
                            $str_condition = "\"AND $col = '\$filter_$col'\"";
                        }
                    }

                    // es..  $str_filter_CAMPO1 = ($filter_CAMPO1 != "") ? "AND CAMPO1 = '$filter_CAMPO1'" : "";
                    $str_filter .= "\$str_filter_$col = (\$filter_$col != \"\") ? $str_condition : \"\";" . PHP_EOL;
                }
            }
            $str_after_filter = ($this->ck_custom_sql2 == 0) ? "SELECT * FROM QRY" : $this->custom_sql2;
            $sql_filter = "
                            WITH QRY AS (   
                                SELECT * FROM (
                                $sql
                                ) WHERE 1= 1
                                $var_filter
                            )
                            $str_after_filter
                            ";

            $param_all = "
                               \$filter = \$app->request->params('filter'); // Param from Post user
                               $var_filter_assign
                               $str_filter
                            ";

            $this->str_filter_dg = $param_all;

            return $sql_filter;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** get string code  fn CrudBase
     *
     * @param type $fn_name
     */
    private function get_api_fn_crud($api_fn_name) {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            if ($this->current_driver == "oci") {

                if ($this->ck_custom_sql == 1) {
                    $sql_select = $this->custom_sql;
                    $sql_select = ($this->enable_filter_dg == 1) ? $this->set_filter2sql($sql_select) : $sql_select;
                } else {
                    $sql_select = $this->get_sql_for_select($this->table_name, $this->table_model); //for template
                    $sql_select = ($this->enable_filter_dg == 1) ? $this->set_filter2sql($sql_select) : $sql_select;
                }

                $param_api_ins = $this->get_param_api_for_insert_update(false); //for template
                $sql_insert = $this->get_sql_for_insert(); //for template
                $param_log_insert = $this->get_param_sql_for_log_insert_update(false);
                $bind_insert = $this->get_param_for_bind_insert_update(false);
                $param_return = $this->get_param_insert_update_return();
                $param_api_upd = $this->get_param_api_for_insert_update(true); //for template
                $param_log_update = $this->get_param_sql_for_log_insert_update(true);
                $sql_update = $this->get_sql_for_update();
                $bind_update = $this->get_param_for_bind_insert_update(true);

                $var_combo = $this->get_code_var_return_combo();
                $int_xls = ($this->ck_model_xls == 1) ? $this->get_field_intestation_for_xls() : "";

                //build template html
                $root_template = $this->root_gii . $this->template_root_path;
                $loader = new Twig_Loader_Filesystem($root_template);
                $twig = new Twig_Environment($loader);

                $php = $twig->render('/crud/api/crud.api.oci.php.twig', array(
                    'api_fn_name' => $api_fn_name
                    , 'sql_select' => $sql_select
                    , 'var_combo' => $var_combo
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
                    , 'str_filter' => $this->str_filter_dg
                    , 'enable_filter' => $this->enable_filter_dg
                    , 'load_dg' => $this->ck_load_dg
                    , 'crud_c' => $this->crud_c
                    , 'crud_r' => $this->crud_r
                    , 'crud_u' => $this->crud_u
                    , 'crud_d' => $this->crud_d
                    , 'int_xls' => $int_xls
                    , 'ck_model_xls' => $this->ck_model_xls
                ));
                $php = str_replace("<?php", "", $php);
                return $php;
            }
            if (($this->current_driver == "pdo") && ($this->type_db == "mysql")) {

                if ($this->ck_custom_sql == 1) {
                    $sql_select = $this->custom_sql;
                    $sql_select = ($this->enable_filter_dg == 1) ? $this->set_filter2sql($sql_select) : $sql_select;
                } else {
                    $sql_select = $this->get_sql_for_select($this->table_name, $this->table_model); //for template
                    $sql_select = ($this->enable_filter_dg == 1) ? $this->set_filter2sql($sql_select) : $sql_select;
                }

                $param_api_ins = $this->get_param_api_for_insert_update(false); //for template
                $sql_insert = $this->get_sql_for_insert(); //for template
                $param_log_insert = $this->get_param_sql_for_log_insert_update(false);
                $bind_insert = $this->get_param_for_bind_insert_update(false);
                $param_return = $this->get_param_insert_update_return();
                $param_api_upd = $this->get_param_api_for_insert_update(true); //for template
                $param_log_update = $this->get_param_sql_for_log_insert_update(true);
                $sql_update = $this->get_sql_for_update();
                $bind_update = $this->get_param_for_bind_insert_update(true);

                $var_combo = $this->get_code_var_return_combo();
                $int_xls = ($this->ck_model_xls == 1) ? $this->get_field_intestation_for_xls() : "";

                //build template html
                $root_template = $this->root_gii . $this->template_root_path;
                $loader = new Twig_Loader_Filesystem($root_template);
                $twig = new Twig_Environment($loader);

                $php = $twig->render('/crud/api/crud.api.pdo.php.twig', array(
                    'api_fn_name' => $api_fn_name
                    , 'sql_select' => $sql_select
                    , 'var_combo' => $var_combo
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
                    , 'drv_cn_var' => $this->pdo_cn_var
                    , 'drv_user_var' => $this->pdo_user_var
                    , 'drv_password_var' => $this->pdo_password_var
                    , 'drv_options_var' => $this->pdo_options_var
                    , 'str_filter' => $this->str_filter_dg
                    , 'enable_filter' => $this->enable_filter_dg
                    , 'load_dg' => $this->ck_load_dg
                    , 'crud_c' => $this->crud_c
                    , 'crud_r' => $this->crud_r
                    , 'crud_u' => $this->crud_u
                    , 'crud_d' => $this->crud_d
                    , 'int_xls' => $int_xls
                    , 'ck_model_xls' => $this->ck_model_xls
                ));
                $php = str_replace("<?php", "", $php);
                return $php;
            }
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
    }

    /** get string code  for js function onAfterEit
     *
     */
    private function get_crud_js_fn__on_after_edit() {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
        $api_url = [];
        $model = $this->table_model;
        $code = "";
        foreach ($model as $value) {
            if ($value["SKIP"] == "0") {
                if ($value["CONSTRAINT_TYPE"] == 'FOREIGN_KEY') {
                    $col = $value["COL"];
                    $code .= "$col" . "__TEXT: row.$col" . "__TEXT,";
                    //row: {COMBO__TEXT: row.COMBO__TEXT}
                }
            }
        }
        $js = "
                                    onAfterEdit: function (index, row) {
                                        $(this).edatagrid('updateRow', {
                                            index: index,
                                            row: { $code }
                                        });
                                    },
                          ";

        return $js;
    }

    /** get string code  function api for crud combo
     *
     */
    private function get_api_fn_combo() {
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;
            $api_url = [];
            $model = $this->table_model;
            $return = [];
            foreach ($model as $value) {
                if ($value["SKIP"] == "0") {
                    if ($value["CONSTRAINT_TYPE"] == 'FOREIGN_KEY') {
                        $table = $value["NAME_TABLE_EXT"];
                        $value_field = $value["VALUE_FIELD"];
                        $text_field = $value["TEXT_FIELD"];
                        $col = $value["COL"];
                        $api_fn_name = "combo_$table" . "__" . $col;
                        if ($value["TYPE"] == "combobox") {
                            $model_combo = $this->get_table_model_from_db($table);
                            if (isset($value["CK_SQL_COMBO"]) && ($value["CK_SQL_COMBO"] == "1")) {
                                $sql_select = $value["SQL_COMBO"];
                            } else {
                                $sql_select = $this->get_sql_for_select($table, $model_combo);
                            }
                        }
                        if ($value["TYPE"] == "combogrid") {
                            $model_combo = json_decode($value["FIELDS"], true);
                            if (isset($value["CK_SQL_COMBO"]) && ($value["CK_SQL_COMBO"] == "1")) {
                                $sql_select = $value["SQL_COMBO"];
                            } else {
                                $sql_select = $this->get_sql_for_select($table, $model_combo);
                            }
                        }

                        //build template html
                        $root_template = $this->root_gii . $this->template_root_path;
                        $loader = new Twig_Loader_Filesystem($root_template);
                        $twig = new Twig_Environment($loader);

                        if ($this->current_driver == "oci") {
                            $php = $twig->render('/crud/api/combobox.api.oci.php.twig', array(
                                'api_fn_name' => $api_fn_name
                                , 'sql_select' => $sql_select
                                , 'table' => $table
                                , 'col' => $col
                                , 'drv_cn_var' => $this->oci_cn_var
                                , 'drv_user_var' => $this->oci_user_var
                                , 'drv_password_var' => $this->oci_password_var
                                , 'drv_charset' => $this->oci_charset
                            ));
                        }
                        if (($this->current_driver == "pdo") && ($this->type_db == "mysql")) {
                            $php = $twig->render('/crud/api/combobox.api.pdo.php.twig', array(
                                'api_fn_name' => $api_fn_name
                                , 'sql_select' => $sql_select
                                , 'table' => $table
                                , 'col' => $col
                                , 'drv_cn_var' => $this->pdo_cn_var
                                , 'drv_user_var' => $this->pdo_user_var
                                , 'drv_password_var' => $this->pdo_password_var
                                , 'drv_options_var' => $this->pdo_options_var
                            ));
                        }

                        $return[] = $php;
                    }
                }
            }
            return $return;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
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
            if (($value["SKIP"] == "0") && ($value["EDIT"] == "1")) {
                $ncol += 1;
                $str_comma = ($ncol > 1) ? ", " : "";
                $str_col .= $str_comma . $col_name; //list col without alias
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
            if (($value["SKIP"] == "0") && ($value["EDIT"] == "1")) {
                ($col_name == $this->primary_key) ? $type_param = "OCI_B_ROWID" : $type_param = "-1";
                ($isUpdate) ? $type_param = "-1" : false;
                $str_bind .= "oci_bind_by_name(\$db, \":$col_name\", \$$col_name, $type_param);" . PHP_EOL;
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
                $str_par .= "'$col_name' => \$$col_name," . PHP_EOL;
            }
        }
        return $str_par;
    }

    /** param for insert for log sql
     *
     * @return string paramsql es ':PARAM1' => $PARAM1,
     */
    private function get_param_sql_for_log_insert_update($add_id) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $str_par = "";
        $model = $this->table_model;
        //while ($row = current($ar)) {
        foreach ($model as $value) {
            $col_name = $value["COL"];
            if ($this->primary_key != $col_name) {
                if (($value["SKIP"] == "0") && ($value["EDIT"] == "1")) {
                    $str_par .= "':" . $col_name . "' => $" . $col_name . "," . PHP_EOL;
                }
            } else { //add primary key
                if ($add_id) {
                    if ($value["SKIP"] == "0") {
                        $str_par .= "':" . $col_name . "' => $" . $col_name . "," . PHP_EOL;
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
                if (($value["SKIP"] == "0") && ($value["EDIT"] == "1")) {
                    $ncol += 1;
                    $str_comma = ($ncol > 1) ? ", " : "";

                    if ($col_type == "datebox") {
                        $col_dt = $this->format_dt2todate(":" . $col_name);
                        $col_name = "$col_name=$col_dt";
                    } else {
                        $col_name = "$col_name=:$col_name";
                    }
                    $str_col .= $str_comma . $col_name; //list col -> :field1, :field2
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
                if (($value["SKIP"] == "0") && ($value["EDIT"] == "1")) {
                    $ncol += 1;
                    $str_comma = ($ncol > 1) ? ", " : "";
                    $col_name = ":" . $col_name;
                    if ($col_type == "datebox") {
                        $col_name = $this->format_dt2todate($col_name);
                    }
                    $str_col .= $str_comma . $col_name; //list col -> :field1, :field2
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
                if ($col != $this->primary_key) {
                    if ($value["SKIP"] == "0") {
                        $bind2var = (isset($value["BIND2VAR"])) ? $value["BIND2VAR"] : "";
                        if ($bind2var != "") {
                            $code .= "\$$col= \"$bind2var\"; // Param from Post user" . PHP_EOL;
                        } else {
                            $code .= "\$$col= \$app->request->params('$col'); // Param from Post user" . PHP_EOL;
                        }
                    }
                } else {
                    if ($add_id) { //primary key
                        if ($value["SKIP"] == "0") {
                            $code .= "\$$col = \$app->request->params('$col'); // Param from Post user" . PHP_EOL;
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

    private function array_unique_multi($array) {
        foreach ($array as $k => $na)
            $new[$k] = serialize($na);
        $uniq = array_unique($new);
        foreach ($uniq as $k => $ser)
            $new1[$k] = unserialize($ser);
        return ($new1);
    }

    /** create rest api
     *
     * @param type $dir directory api
     * @param type $api_url string rest api url
     * @param type $fn_api string rest functions
     */
    private function union_api_code($dir, $api_url, $fn_api) {
        ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $fn_api = $this->array_unique_multi($fn_api);

        $api_url_all = "";
        for ($i = 0; $i <= Count($api_url) - 1; $i++) {
            $api_url_all .= $api_url[$i] . PHP_EOL;
        }
        $fn_api_all = "";
        $i = 0;
        for ($i = 0; $i <= Count($fn_api) - 1; $i++) {
            $fn_api_all .= $fn_api[$i] . PHP_EOL;
        }

        $file1_api = $this->root_gii . $this->template_base_path . "/api/api_1_declare.php";
        $api_declare = file_get_contents($file1_api);

        $file2_api = $this->root_gii . $this->template_base_path . "/api/api_2_fn.php";
        $api_fn = str_replace("<?php", "", file_get_contents($file2_api));


        $api = $api_declare . PHP_EOL . $api_url_all . PHP_EOL . $api_fn . PHP_EOL . $fn_api_all; //create File Api


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
            $this->create_folder($dir . "/api/data");
            $this->create_folder($dir . "/js");

            $zip_file = $this->root_gii . $this->template_base_path . '/lib.zip';
            $this->unzip($zip_file, $dir);
            $zip_file = $this->root_gii . $this->template_base_path . '/css.zip';
            $this->unzip($zip_file, $dir);

            if ($this->ck_model_xls == 0) {
                $zip_file = $this->root_gii . $this->template_base_path . '/vendor.zip';
                $this->unzip($zip_file, $dir . "/");
                $composer_path = $this->root_gii . $this->template_base_path;
            }
            if ($this->ck_model_xls == 1) {
                $zip_file = $this->root_gii . $this->template_excel_path . '/vendor.zip';
                $this->unzip($zip_file, $dir . "/");
                $composer_path = $this->root_gii . $this->template_excel_path;
            }
            $template_path = $this->root_gii . $this->template_base_path;
            $ar_files = [
                [$template_path . "/LICENSE", $dir . "/"],
                [$composer_path . "/composer.json", $dir . "/"],
                [$template_path . "/.htaccess", $dir . "/"], //for disable cache javascript
                [$template_path . "/api/.htaccess", $dir . "/api/"],
                [$template_path . "/api/fn_api.php", $dir . "/api/"],
                [$template_path . "/js/fn_base.js", $dir . "/js/"],
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
        try {
            ($this->debug_on_file) ? error_log(logTime() . basename(__FILE__) . "   " . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


            $zip = new ZipArchive;
            $file = $zip->open($zip_file);
            $zip->extractTo($dir);
            $zip->close();
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'logs/error.log');
            error_log(LogTime() . " " . "\$zip_file -> $zip_file", 3, 'logs/error.log');
            error_log(LogTime() . " " . "\$dir -> $dir", 3, 'logs/error.log');
            throw new Exception(message_err($e));
        }
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
