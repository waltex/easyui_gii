<?php

//namespace easyuigii;

class easyuigii {

    private $template_base_path = "/src/template/base";
    private $template_root_path = "/src/template";
    private $ar_col_type = []; // for tamplate crud
    private $primary_key = ""; // auto find from table structure
    private $language_default = ''; // form asset template
    private $app_setting = []; // array app setting from json file
    private $host_api = "api"; // for remote/local host es. (local) api or remote) http:/192.168.20/easui_gii/api
    public $app_name = "";
    public $app_folder = "";
    public $table_name = "";
    public $cols_table_skip = ["ID2"]; //skip col for crud (select/insert/update) and javascript
    public $cols_table_hide = []; //hide col only on code javascript and not to sql
    public $date_format = "DD-MM-YYYY";
    public $html_prefix = "1";
    public $api_url = "/crud/ABB_CRUD";
    public $api_fn_name = "crud_ABB_CRUD";
    public $dg_col_px_auto = true; //auto calc px, if false not set with length for datagrid col
    public $dg_cols_ck = ["ATTIVO"]; //cols datagrid with checkbox

    /**
     */

    function __construct() {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $this->script_path = str_replace('/src/class', '', str_replace('\\', '/', __DIR__)); //apllication path
        $ar_file = $this->get_ar_app_setting();
        $this->app_setting = $ar_file;
        $this->write_to_file_db_settings();
    }

    function on_begin_crud() {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $this->primary_key = $this->get_primary_key();
    }

    function get_param_db_setting($value) {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $pos = strpos($value, " ");
        $param = substr($value, $pos, strlen($value));
        return $param;
    }

    // return id key of setting db es #1
    function get_n_db_setting($value) {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $pos = strpos($value, " ");
        $n = substr($value, 0, $pos);
        return $n;
    }

    /** write to file db_setting the variable of setting
     */
    function write_to_file_db_settings() {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $setting = $this->app_setting;

        $n_db = [];
        //set list id es.#1,#2,#3
        foreach ($setting as $key => $value) {
            $find = substr($key, 0, 1);
            if ($find == '#') {
                $n = $this->get_n_db_setting($key);
                if (!in_array($n, $n_db)) {
                    $n_db[] = $n;
                }
            }
        }
    }

    /** return array app setting
     */
    function get_ar_app_setting() {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $file = $this->script_path . "/app_setting.json";
        $imp = file_get_contents($file);
        $ar_file = json_decode($imp, true);
        return $ar_file;
    }

    /** folder create and file
     */
    public function build_app_crud() {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $this->on_begin_crud();

        $dir = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->app_folder; //code path output
        $this->create_folder($dir);

        //build template html
        $root_template = $this->script_path . $this->template_root_path;
        $loader = new Twig_Loader_Filesystem($root_template);
        $twig = new Twig_Environment($loader);
        //Transalte Tamplate with Function T - Very Super
        $function = new Twig_SimpleFunction('T', function ($value) {
            return $this->T($value);
        });
        $twig->addFunction($function);

        $this->copy_file_framework($dir); // copy file/folder framework
        //create asset template
        $html = $twig->render('/base/asset.html.twig', array(
            'language_default' => $this->app_setting["lingua corrente"] // current language
        ));
        $file = $dir . "/asset.html";
        file_put_contents($file, $html); //write generated html
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
            , 'id' => $this->primary_key
        ));
        $file = $dir . "/js/index.js";
        file_put_contents($file, $js); //write generated html
        //create api
        $this->set_api($dir, $api_url, $fn_api); //create file api
    }

    private function get_template_js_crud() {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $ar = $this->ar_col_type;
        $key_row = [];
        $ar2 = []; //ord aray with ID/PRIMARY KEY with first element
        while ($row = current($ar)) {
            $key = key($row);
            if ($key == $this->primary_key) {
                $key_row = $row;
            } else {
                $ar2[] = $row;
            }
            next($ar);
        }
        $ar = array_merge([$key_row], $ar2);

        $code = "";
        while ($row = current($ar)) {
            $col = key($row);
            $type = $row[$col];
            if (!in_array($col, $this->cols_table_hide)) {
                $code.= $this->get_js_crud_col($col, $type);
            }
            next($ar);
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
    private function get_js_crud_col($col, $type) {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $with = "";
        $colt = $this->T($col); //translate
        if ($this->dg_col_px_auto) {
            $px = (strlen($colt) * 15) . "px";
            $with = "width: '$px',";
        }

        if (in_array($col, [$this->primary_key])) {
            $ck = "{field: 'ck', checkbox: true}," . PHP_EOL;
            return $ck . "{field: '$col', title: '$colt', $with sortable: true}," . PHP_EOL;
        }

        if (in_array($col, $this->dg_cols_ck)) {
            //{field: 'ID_CLONE', title: 'Fase<br>Duplicata', formatter: mycheck},
            return "{field: '$col', title: '$colt', editor: {type: 'checkbox', options: {on: '1', off: '0',formatter: mycheck,required: true}}}," . PHP_EOL;
        }

        if (in_array($type, ['VARCHAR2', 'VARCHAR'])) {
            return "{field: '$col', title: '$colt', $with editor: {type: 'textbox', options: {required: true}}, sortable: true}," . PHP_EOL;
        }
        if (in_array($type, ['NUMBER'])) {
            $with = "width: '50px',";
            return "{field: '$col', title: '$colt', $with editor: {type: 'numberbox', options: {required: true}}, sortable: true}," . PHP_EOL;
        }
        if (in_array($type, ['DATE'])) {
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
            include 'api_setup.php';
            ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

            $app = Slim\Slim::getInstance();

            $table = $this->table_name;
            $sql = "
                SELECT * FROM $table
                ";

            error_log(LogTime() . ' Sql, get column field: ' . PHP_EOL . $sql . PHP_EOL, 3, 'logs/sql.log');

            $conn = oci_connect($db4_user, $db4_psw, $db4_GOLD, 'UTF8');
            $db = oci_parse($conn, $sql);
            $rs = oci_execute($db);

            $ncols = oci_num_fields($db);

            $this->ar_col_type = [];
            $str_col_w_a = "";
            $str_col = "";
            $ncol = 0;
            for ($i = 1; $i <= $ncols; $i++) {
                $col_name = oci_field_name($db, $i);
                $col_name_w_a = "A." . $col_name;
                $col_type = oci_field_type($db, $i);
                //skip cols
                if (!in_array($col_name, $this->cols_table_skip)) {
                    $ncol+=1;
                    array_push($this->ar_col_type, [$col_name => $col_type]);
                    if ($col_type == "DATE") {
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

    /** get primary key
     *
     * @return type string primary key
     */
    private function get_primary_key() {
        try {
            include 'api_setup.php';
            ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

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

            $conn = oci_connect($db4_user, $db4_psw, $db4_GOLD, 'UTF8');
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
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        return "TO_CHAR($filed,'" . $this->date_format . "') $col_name";
    }

    /** to_char with date
     *
     * @param type $field field

     * @return type string es. (:FIELD1,'YYYY-MM-DD')
     */
    private function format_dt2todate($filed) {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        return "TO_DATE($filed,'" . $this->date_format . "')";
    }

    /** get string code  fn CrudBase
     *
     * @param type $fn_name
     */
    private function get_api_fn_crud($api_fn_name) {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


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
        $root_template = $this->script_path . $this->template_root_path;
        $loader = new Twig_Loader_Filesystem($root_template);
        $twig = new Twig_Environment($loader);

        $php = $twig->render('/crud/api/crud.api.php.twig', array(
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
        ));
        $php = str_replace("<?php", "", $php);
        return $php;
    }

    /** list col from type col table es. field1, field2, field3
     */
    private function get_list_col() {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        $str_col = "";
        $ncol = "0";
        $ar = $this->ar_col_type;
        while ($row = current($ar)) {
            $col_name = key($row);
            $type = $row[$col_name];
            if (!in_array($col_name, $this->cols_table_skip)) {
                $ncol+=1;
                $str_comma = ($ncol > 1) ? ", " : "";
                $str_col.=$str_comma . $col_name; //list col without alias
            }
            next($ar);
        }
        return $str_col;
    }

    /** param for bind insert/update
     *
     * @return string paramsql es oci_bind_by_name($db, ":ID", $ID, OCI_B_ROWID);
     */
    private function get_param_for_bind_insert_update($isUpdate) {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $pk = $this->primary_key;
        $str_bind = "";
        $ar = $this->ar_col_type;
        while ($row = current($ar)) {
            $col_name = key($row);
            $col_type = $row[$col_name];
            if (!in_array($col_name, $this->cols_table_skip)) {
                ($col_name == $pk) ? $type_param = "OCI_B_ROWID" : $type_param = "-1";
                ($isUpdate) ? $type_param = "-1" : false;
                $str_bind.="oci_bind_by_name(\$db, \":$col_name\", \$$col_name, $type_param);" . PHP_EOL;
            }
            next($ar);
        }
        return $str_bind;
    }

    /** param for insert - return array
     *
     * @return string paramsql es 'PARAM1' => $PARAM1,
     */
    private function get_param_insert_update_return() {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $str_par = "";
        $ar = $this->ar_col_type;
        while ($row = current($ar)) {
            $col_name = key($row);
            $col_type = $row[$col_name];
            if (!in_array($col_name, $this->cols_table_skip)) {
                $str_par.= "'$col_name' => \$$col_name," . PHP_EOL;
            }
            next($ar);
        }
        return $str_par;
    }

    /** param for insert for log sql
     *
     * @return string paramsql es ':PARAM1' => $PARAM1,
     */
    private function get_param_sql_for_log_insert_update($add_idd) {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $str_par = "";
        $ar = $this->ar_col_type;
        while ($row = current($ar)) {
            $col_name = key($row);
            $col_type = $row[$col_name];
            if ($this->primary_key != $col_name) {
                if (!in_array($col_name, $this->cols_table_skip)) {
                    $str_par.="':" . $col_name . "' => $" . $col_name . "," . PHP_EOL;
                }
            } else { //add primary key
                if ($add_idd) {
                    if (!in_array($col_name, $this->cols_table_skip)) {
                        $str_par.="':" . $col_name . "' => $" . $col_name . "," . PHP_EOL;
                    }
                }
            }

            next($ar);
        }
        return $str_par;
    }

    /** get sql for update
     */
    private function get_sql_for_update() {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $str_col = "";
        $ncol = "0";
        $ar = $this->ar_col_type;
        while ($row = current($ar)) {
            $col_name = key($row);
            $col_type = $row[$col_name];
            if ($this->primary_key != $col_name) {
                if (!in_array($col_name, $this->cols_table_skip)) {
                    $ncol+=1;
                    $str_comma = ($ncol > 1) ? ", " : "";

                    if ($col_type == "DATE") {
                        $col_dt = $this->format_dt2todate(":" . $col_name);
                        $col_name = "$col_name=$col_dt";
                    } else {
                        $col_name = "$col_name=:$col_name";
                    }
                    $str_col.=$str_comma . $col_name; //list col -> :field1, :field2
                }
            }
            next($ar);
        }
        $table = $this->table_name;
        $pk = $this->primary_key;

        $sql = "UPDATE $table SET $str_col WHERE $pk=:$pk";
        return $sql;
    }

    /** list col from type col table for insert sql es. field1, field2, field3
     */
    private function get_list_col_for_insert_values() {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $str_col = "";
        $ncol = "0";
        $ar = $this->ar_col_type;
        while ($row = current($ar)) {
            $col_name = key($row);
            $col_type = $row[$col_name];
            if ($this->primary_key != $col_name) {
                if (!in_array($col_name, $this->cols_table_skip)) {
                    $ncol+=1;
                    $str_comma = ($ncol > 1) ? ", " : "";
                    $col_name = ":" . $col_name;
                    if ($col_type == "DATE") {
                        $col_name = $this->format_dt2todate($col_name);
                    }
                    $str_col.=$str_comma . $col_name; //list col -> :field1, :field2
                }
            }
            next($ar);
        }
        return $str_col;
    }

    private function get_sql_for_insert() {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


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
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;

        try {
            $code = "";
            $ar = $this->ar_col_type;
            while ($row = current($ar)) {
                $key = key($row);
                $value = $row[$key];
                if ($key != $this->primary_key) {
                    $code.= "\$$key = \$app->request->params('$key'); // Param from Post user" . PHP_EOL;
                } else {
                    if ($add_id) { //primary key
                        $code.= "\$$key = \$app->request->params('$key'); // Param from Post user" . PHP_EOL;
                    }
                }
                next($ar);
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
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        return '$' . "app->post('$url', '$fn'); ";
    }

    /** create rest api
     *
     * @param type $dir directory api
     * @param type $api_url string rest api url
     * @param type $fn_api string rest functions
     */
    private function set_api($dir, $api_url, $fn_api) {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $file1_api = $this->script_path . $this->template_base_path . "/api/api_1_declare.php";
        $api_declare = file_get_contents($file1_api);

        $file2_api = $this->script_path . $this->template_base_path . "/api/api_2_fn.php";
        $api_fn = str_replace("<?php", "", file_get_contents($file2_api));

        $api = $api_declare . PHP_EOL . $api_url . PHP_EOL . $api_fn . PHP_EOL . $fn_api; //create File Api
        $file = $dir . "/api/api.php";
        file_put_contents($file, $api); //write api
    }

    /** copy file to directory
     * @param type $ar_files array files es. [[full_path_file_from, dir_to],[full_path_file_from, dir_to]....]

     */
    function copy_files_to_dir($ar_files) {
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


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
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        try {
            $this->create_folder($dir . "/api");

            $zip_file = $this->script_path . $this->template_base_path . '/lib.zip';
            $this->unzip($zip_file, $dir);
            $zip_file = $this->script_path . $this->template_base_path . '/css.zip';
            $this->unzip($zip_file, $dir);
            $zip_file = $this->script_path . $this->template_base_path . '/js.zip';
            $this->unzip($zip_file, $dir);


            $zip_file = $this->script_path . $this->template_base_path . '/vendor.zip';
            $this->unzip($zip_file, $dir . "/");

            $template_path = $this->script_path . $this->template_base_path;
            $ar_files = [
                [$template_path . "/LICENSE", $dir . "/"],
                [$template_path . "/composer.json", $dir . "/"],
                [$template_path . "/.htaccess", $dir . "/"], //for disable cache javascript
                [$template_path . "/api/.htaccess", $dir . "/api/"],
                [$template_path . "/api/fn_api.php", $dir . "/api/"],
                [$template_path . "/api/api_setup.php", $dir . "/api/"],
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
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


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
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


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
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


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
        include 'api_setup.php';
        ($write_log) ? error_log(logTime() . __FUNCTION__ . PHP_EOL, 3, 'logs/fn.log') : false;


        $file = $this->script_path . "/app_setting.json";
        $imp = file_get_contents($file);
        $ar_file = json_decode($imp, true);
        $lang2from = $ar_file["traduci dalla lingua"];
        $lang2to = $ar_file["traduci alla lingua"];
        $langDefault = $ar_file["lingua corrente"];



        $file = $this->script_path . "/language/$lang2from" . "2" . "$lang2to.json";
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
