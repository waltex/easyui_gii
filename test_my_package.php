<?php


$loader = include __DIR__ . './vendor/autoload.php';
//include './api/../lib/easyui_gii/class.crud.php';


$app_name = "dg1";
$app_folder = "/out/dg1";
$table_name = "test1";
$opt = [];

$crud = new \easyuigii\easyuigii($app_name, $app_folder, $table_name, $opt);
$crud->test();

