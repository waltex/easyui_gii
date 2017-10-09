<?php

function add_col_combo($ar_combo, $ar_dg, $field_dg, $value_field, $text_field) {
    $key = array_search(["ID" => 2], $ar_combo);
    $text = $ar_combo[$key]["DESCRI"];
    $ar_dg2 = [];
    foreach ($ar_dg as $value) {
        $find = $value[$field_dg];
        $key = array_search([$value_field => $find], $ar_combo);
        $text = $ar_combo[$key][$text_field];
        $value[$field_dg . "_DESC"] = $text;
        array_push($ar_dg2, $value);
    }
    return $ar_dg2;
}
