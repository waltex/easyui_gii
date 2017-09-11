<?php

namespace easyuigii;

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
        $this->app_name = $app_name;
        $this->app_folder = $app_folder;
        $this->table_name = $table_name;
    }

    // metodi
    public function render() {
        echo "test class ";
    }

}
