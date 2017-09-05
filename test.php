<?php

// base class with member properties and methods
class Vegetable {

    var $edible;
    var $color;

    function __construct($edible, $color = "green") {
        $this->edible = $edible;
        $this->color = $color;
    }

    function is_edible() {
        return $this->edible;
    }

    function what_color() {
        return $this->color;
    }

}

// end of class Vegetable

$ob = new Vegetable(true);
$ob->color = "red";

$test = $ob->color;
$color = $ob->what_color();

$stop = 1;
