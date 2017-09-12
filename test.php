<?php

echo 'ddd';
$dir = "/test";
$file = $_SERVER['DOCUMENT_ROOT'] . "/" . 'easyui_gii/src/template/base/lib.zip';
$zip = new ZipArchive;
$zip->open($file);
//if ($zip->open($file) === TRUE) {
$zip->extractTo($dir);
    $zip->close();
    //} else {
//    echo "failed";
//}
