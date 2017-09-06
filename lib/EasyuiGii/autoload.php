<?php

  function autoload($class_name) {
  $filename = "\class." . $class_name . ".php";
    //$filename = "\" . $class_name . ".php";
    if (is_readable(__DIR__ . $filename)) {
  require __DIR__ . $filename;
  }
  }
  spl_autoload_register("autoload");
 




