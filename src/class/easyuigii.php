<?php

//namespace easyuigii;

class easyuigii {

    private $template_base_path = "/src/template/base";
    private $template_root_path = "/src/template";
    private $ar_col_type = []; // for tamplate crud
    public $app_name = "";
    public $app_folder = "";
    public $table_name = "";
    public $date_format = "DD-MM-YYYY";
    public $html_prefix = "1";
    public $api_url = "/crud/ABB_CRUD";
    public $api_fn_name = "crud_ABB_CRUD";
    public $dg_col_px_auto = true; //auto calc px, if false not set with length for datagrid col
    public $dg_cols_ck = ["ATTIVO"]; //cols datagrid with checkbox

    /**
     */

    function __construct() {
        $this->script_path = str_replace('/src/class', '', str_replace('\\', '/', __DIR__)); //apllication path
    }

    /** folder create and file
     */
    public function build_app_crud() {
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

        $this->set_template_base($dir); // set template base
        //
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
            , 'api_url' => $this->api_url
            , 'title' => $this->app_name
            , 'col_crud' => $this->get_template_js_crud() //this function use after $this->get_api_fn_crud
        ));
        $file = $dir . "/js/index.js";
        file_put_contents($file, $js); //write generated html
        //create api
        $this->set_api_base($dir, $api_url, $fn_api); //create file api
    }

    private function get_template_js_crud() {
        $ar = $this->ar_col_type;
        $code = "";
        while ($row = current($ar)) {
            $key = key($row);
            $value = $row[$key];
            $code.= $this->get_js_crud_col($key, $value);
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
        $with = "";
        if ($this->dg_col_px_auto) {
            $px = (strlen($col) * 15) . "px";
            $with = "width: '$px',";
        }

        if (in_array($col, ['ID'])) {
            $ck = "{field: 'ck', checkbox: true}," . PHP_EOL;
            return $ck . "{field: '$col', title: '$col', $with sortable: true}," . PHP_EOL;
        }

        if (in_array($col, $this->dg_cols_ck)) {
            //{field: 'ID_CLONE', title: 'Fase<br>Duplicata', formatter: mycheck},
            return "{field: '$col', title: '$col', editor: {type: 'checkbox', options: {formatter: mycheck,required: true}}}," . PHP_EOL;
        }

        if (in_array($type, ['VARCHAR2', 'VARCHAR'])) {
            return "{field: '$col', title: '$col', $with editor: {type: 'textbox', options: {required: true}}, sortable: true}," . PHP_EOL;
        }
        if (in_array($type, ['NUMBER'])) {
            $with = "width: '50px',";
            return "{field: '$col', title: '$col', $with editor: {type: 'numberbox', options: {required: true}}, sortable: true}," . PHP_EOL;
        }
        if (in_array($type, ['DATE'])) {
            $with = "width: '100px',";
            ($this->date_format = "DD-MM-YYYY") ? $type_dt = "it" : $type_dt = "en";
            $date_format = "formatter: myformatter_d_$type_dt, parser: myparser_d_$type_dt,";
            return "{field: '$col', title: '$col', $with editor: {type: 'datebox', options: { $date_format required: true}}, sortable: true}," . PHP_EOL;
        }
        return "{field: '$col', title: '$col', $with editor: {type: '??$type??', options: {required: true}}, sortable: true}," . PHP_EOL;
    }

    /** create sql string
     *
     * @return type string
     */
    private function get_select_from_table() {
        try {

            $app = Slim\Slim::getInstance();
            include 'api_setup.php';
            $table = $this->table_name;
            $sql = "
                SELECT * FROM $table
                ";

            ($debug) ? error_log(LogTime() . ' Sql, get column field: ' . PHP_EOL . $sql . PHP_EOL, 3, 'debug.log') : false;

            $conn = oci_connect($db4_user, $db4_psw, $db4_GOLD, 'UTF8');
            $db = oci_parse($conn, $sql);
            $rs = oci_execute($db);

            $ncols = oci_num_fields($db);

            $this->ar_col_type = [];
            $strCol = "";
            for ($i = 1; $i <= $ncols; $i++) {
                $col_name = oci_field_name($db, $i);
                $col_name_w_a = "A." . $col_name;
                $col_type = oci_field_type($db, $i);
                array_push($this->ar_col_type, [$col_name => $col_type]);
                //$this->ar_col_type[] = [$col_name => $col_type]; //save type col
                if ($col_type == "DATE") {
                    $col_name_w_a = $this->format_date_select($col_name_w_a, $col_name);
                }
                $strComma = ($i < $ncols) ? ", " : "";
                $strCol.=$col_name_w_a . $strComma;
            }
            $strSql = "SELECT $strCol FROM $table A";
            return $strSql;
        } catch (Exception $e) {
            error_log(LogTime() . " " . message_err($e), 3, 'error.log');
            throw new Exception(message_err($e));
        }
    }

    private function format_date_select($col_name_w_a, $col_name) {
        return "TO_CHAR($col_name_w_a,'" . $this->date_format . "') $col_name";
    }

    /** get string code  fn CrudBase
     *
     * @param type $fn_name
     */
    private function get_api_fn_crud($api_fn_name) {

        $sql = $this->get_select_from_table();

        //build template html
        $root_template = $this->script_path . $this->template_root_path;
        $loader = new Twig_Loader_Filesystem($root_template);
        $twig = new Twig_Environment($loader);

        $php = $twig->render('/crud/api/crud.api.php.twig', array('api_fn_name' => $api_fn_name, "sql" => $sql));
        $php = str_replace("<?php", "", $php);
        return $php;
    }

    /** get string code  api name
     *
     * @param type $url endpoint url
     * @param type $fn function associate to api
     * @return type string
     */
    private function get_api_name($url, $fn) {
        return '$' . "app->post('$url', '$fn'); ";
    }

    /** create api with only base function
     * @param type $dir directory app
     */
    private function set_api_base($dir, $api_url, $fn_api) {
        $file1_api = $this->script_path . $this->template_base_path . "/api/api_1_declare.php";
        $api_declare = file_get_contents($file1_api);

        $file2_api = $this->script_path . $this->template_base_path . "/api/api_2_fn.php";
        $api_fn = str_replace("<?php", "", file_get_contents($file2_api));

        $api = $api_declare . PHP_EOL . $api_url . PHP_EOL . $api_fn . PHP_EOL . $fn_api; //create File Api
        $file = $dir . "/api/api.php";
        $this->create_folder($dir . "/api");
        file_put_contents($file, $api); //write api

        $this->copy_api_base($dir); //copy function and htaccess
    }

    /** copy file api base function and htaccess
     * @param type $dir directory app
     */
    private function copy_api_base($dir) {
        $file_htaccess = $this->script_path . $this->template_base_path . "/api/.htaccess";
        $file_htaccess_to = $dir . "/api/.htaccess";
        copy($file_htaccess, $file_htaccess_to);

        $file_fn_api = $this->script_path . $this->template_base_path . "/api/fn_api.php";
        $file_fn_api_to = $dir . "/api/fn_api.php";
        copy($file_fn_api, $file_fn_api_to);

        $zip_file = $this->script_path . $this->template_base_path . '/vendor.zip';
        $this->unzip($zip_file, $dir . "/");

        $file_api_setup = $this->script_path . $this->template_base_path . "/api/api_setup.php";
        $file_api_setup_to = $dir . "/api/api_setup.php";
        copy($file_api_setup, $file_api_setup_to);
    }

    /** copy file (css, js, ....) for template base
     * @param type $dir directory app
     */
    private function set_template_base($dir) {
        $zip_file = $this->script_path . $this->template_base_path . '/lib.zip';
        $this->unzip($zip_file, $dir);
        $zip_file = $this->script_path . $this->template_base_path . '/root.zip';
        $this->unzip($zip_file, $dir);
        $zip_file = $this->script_path . $this->template_base_path . '/css.zip';
        $this->unzip($zip_file, $dir);
        $zip_file = $this->script_path . $this->template_base_path . '/js.zip';
        $this->unzip($zip_file, $dir);
    }

    /** create folder recursive and delete file if exists
     * @param type $dir directory
     */
    private function create_folder($dir) {
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
        $file = $this->script_path . "/app_setting.json";
        $imp = file_get_contents($file);
        $ar_file = json_decode($imp, true);
        $lang2from = $ar_file["language from translate"];
        $lang2to = $ar_file["language to translate"];
        $langDefault = $ar_file["language default"];



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
