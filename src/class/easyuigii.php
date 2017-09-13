<?php

//namespace easyuigii;

class easyuigii {

    public $app_name = "";
    public $app_folder = "";
    public $table_name = "";

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

        //build template
        $loader = new Twig_Loader_Filesystem($this->script_path . '/src/template');
        $twig = new Twig_Environment($loader);
        $prefix = 20; // prefix id es. #dg1 #tb1
        $html = $twig->render('base/index.html', array('url_body' => 'crud/body.crud.html', 'n' => $prefix));
        $file = $dir . "/index.html";
        file_put_contents($file, $html); //write generated html


        $zip_file = $this->script_path . '/src/template/base/lib.zip';
        $this->unzip($zip_file, $dir);
        $zip_file = $this->script_path . '/src/template/base/root.zip';
        $this->unzip($zip_file, $dir);
        $zip_file = $this->script_path . '/src/template/base/css.zip';
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

}
