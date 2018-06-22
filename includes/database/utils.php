<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 31.01.18
 * Time: 21:49
 */


function array_map_meld(string $glue, array $array1, array $array2) : array {
    return array_map(
        function ($el1, $el2) use ($glue) { return $el1 . $glue . $el2; },
        $array1,
        $array2
    );
}

function array_map_prefix(string $prefix, array $array) : array {
    return array_map(
        function ($el) use ($prefix) { return $prefix . $el; },
        $array
    );
}

function array_map_wrap(string $wrapper, array $array) : array {
    return array_map (
        function ($el) use ($wrapper) { return $wrapper . $el . $wrapper; },
        $array
    );
}
