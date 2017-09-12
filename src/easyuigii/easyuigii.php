<?php

//namespace easyuigii;

class easyuigii {
    // proprietà
    //public $a = 10;

    /**
     * @param string $name_app name application
     * @param string $folder_app path outuput code generated
     * @param string $table_name  table name
     * @param array $opt additional option
     */
    function __construct($app_name, $app_folder, $table_name, $opt) {
        $this->script_path = str_replace('src/easyuigii', '', str_replace('\\', '/', __DIR__)); //apllication path
        $this->app_name = $app_name;
        $this->app_folder = $_SERVER['DOCUMENT_ROOT'] . "/" . $app_folder; //code path output
        $this->table_name = $table_name;
    }

    /** folder create and file
     */
    public function buildAppCrud() {
        $dir = $this->app_folder;
        //folder create
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); //create folder
        } else {
            $this->rrmdir($dir); //empty
            mkdir($dir, 0777, true); //create folder and folder below
        }
        //build template
        $loader = new Twig_Loader_Filesystem('../src/template');
        $twig = new Twig_Environment($loader);
        $html = $twig->render('crud.html', array('test' => 'Fabien'));
        $file = $dir . "/index.htm";
        file_put_contents($file, $html); //write generated html

        $zip = new ZipArchive;
        $zip_file = $this->script_path . '/src/template/base/lib.zip';
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
