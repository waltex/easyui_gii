<?php

//namespace easyuigii;

class easyuigii {

    public $app_name = "demo";
    public $app_folder = "demo";
    public $table_name = "";
    public $htmlPrefix = "1";

    /**
     */
    function __construct() {
        $this->script_path = str_replace('src/class', '', str_replace('\\', '/', __DIR__)); //apllication path
    }

    /** folder create and file
     */
    public function buildAppCrud() {
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->app_folder; //code path output
        $this->create_folder($dir);

        //build template html
        $loader = new Twig_Loader_Filesystem($this->script_path . '/src/template');
        $twig = new Twig_Environment($loader);
        //Transalte Tamplate with Function T - Very Super
        $function = new Twig_SimpleFunction('T', function ($value) {
            return $this->T($value);
        });
        $twig->addFunction($function);
        $function = new Twig_SimpleFunction('F', function ($value) {
            return $value;
        });
        $twig->addFunction($function);


        $html = $twig->render('base/index.html', array('url_body' => 'crud/body.crud.html', 'n' => $this->htmlPrefix));
        $file = $dir . "/index.html";
        file_put_contents($file, $html); //write generated html


        $zip_file = $this->script_path . '/src/template/base/lib.zip';
        $this->unzip($zip_file, $dir);
        $zip_file = $this->script_path . '/src/template/base/root.zip';
        $this->unzip($zip_file, $dir);
        $zip_file = $this->script_path . '/src/template/base/css.zip';
        $this->unzip($zip_file, $dir);
        $zip_file = $this->script_path . '/src/template/base/js.zip';
        $this->unzip($zip_file, $dir);

        $html = $twig->render('crud/index.crud.js', array('n' => $this->htmlPrefix));
        $file = $dir . "/js/index.js";
        file_put_contents($file, $html); //write generated html
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
        $file = $this->script_path . "app_setting.json";
        $imp = file_get_contents($file);
        $ar_file = json_decode($imp, true);
        $lang2from = $ar_file["language from translate"];
        $lang2to = $ar_file["language to translate"];
        $langDefault = $ar_file["language default"];



        $file = $this->script_path . "language/$lang2from" . "2" . "$lang2to.json";
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
