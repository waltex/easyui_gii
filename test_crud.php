<?php

include './lib/easyui_gii/autoload.php';
use easyui_gii\crud\crud;

//include './api/../lib/easyui_gii/class.crud.php';

$app_name = "dg1";
$app_folder = "/out/dg1";
$table_name = "test1";
$opt = [];



$crud = new crud($app_name, $app_folder, $table_name, $opt);
$crud->render();

