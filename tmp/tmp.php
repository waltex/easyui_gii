<?php

function add_col_combo($ar_combo, $ar_dg, $field_dg, $value_field, $text_field) {
    $ar_combo2 = [];
    foreach ($ar_combo as $value) {
        $key = $value[$value_field];
        $text = $value[$text_field];
        $ar_combo2[$key] = $text;
    }
    $txt = 1;
}
