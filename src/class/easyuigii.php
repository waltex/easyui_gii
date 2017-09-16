<?php

//namespace easyuigii;

class easyuigii {

    private $template_base_path = "/src/template/base";
    private $template_root_path = "/src/template";
    public $app_name = "demo";
    public $app_folder = "demo";
    public $table_name = "";
    public $htmlPrefix = "1";
    public $ApiUrl = "/crud/ABB_CRUD";
    public $ApiFn = "crud_ABB_CRUD";

    /**
     */
    function __construct() {
        $this->script_path = str_replace('/src/class', '', str_replace('\\', '/', __DIR__)); //apllication path
    }

    /** folder create and file
     */
    public function buildAppCrud() {
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
        //create page js and html
        $html = $twig->render('/base/index.html', array('url_body' => 'crud/body.crud.html', 'n' => $this->htmlPrefix));
        $file = $dir . "/index.html";
        file_put_contents($file, $html); //write generated html

        $html = $twig->render('/crud/index.crud.js', array('n' => $this->htmlPrefix, 'apiUrl' => $this->ApiUrl));
        $file = $dir . "/js/index.js";
        file_put_contents($file, $html); //write generated html


        $ApiUrl = $this->getApiName($this->ApiUrl . "/:command", $this->ApiFn); //create code url api + function
        $fnApi = $this->getApiFn_Crud($this->ApiFn); //template redered api function
        //create api
        $this->set_api_base($dir, $ApiUrl, $fnApi); //create file api
    }

    /** get string code  fn CrudBase
     *
     * @param type $fn_name
     */
    private function getApiFn_Crud($fn_name) {
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->app_folder; //code path output
        //build template html
        $root_template = $this->script_path . $this->template_root_path;
        $loader = new Twig_Loader_Filesystem($root_template);
        $twig = new Twig_Environment($loader);
        $php = $twig->render('/crud/api/crud.api.php', array('ApiFnName' => $fn_name));
        $php = str_replace("<?php", "", $php);
        return $php;
    }

    /** get string code  api name
     *
     * @param type $url endpoint url
     * @param type $fn function associate to api
     * @return type string
     */
    private function getApiName($url, $fn) {
        return '$' . "app->post('$url', '$fn'); ";
    }

    /** create api with only base function
     * @param type $dir directory app
     */
    private function set_api_base($dir, $ApiUrl, $fnApi) {
        $file1_api = $this->script_path . $this->template_base_path . "/api/api_1_declare.php";
        $api_declare = file_get_contents($file1_api);

        $file2_api = $this->script_path . $this->template_base_path . "/api/api_2_fn.php";
        $api_fn = str_replace("<?php", "", file_get_contents($file2_api));

        $api = $api_declare . PHP_EOL . $ApiUrl . PHP_EOL . $api_fn . PHP_EOL . $fnApi; //create File Api
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

    /** create folder if not exists or delete file
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
