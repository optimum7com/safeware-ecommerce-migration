<?php

use XEngine\Support\Debug\Dumper;

if (! function_exists('xdd')) {
    function xdd()
    {
        array_map(function ($x) {
            (new Dumper)->dump($x);
        }, func_get_args());

        die(1);
    }
}
if (! function_exists('xddd')) {
    function xddd()
    {
        array_map(function ($x) {
            (new Dumper)->dump($x);
        }, func_get_args());
    }
}