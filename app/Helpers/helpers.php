<?php

if (!function_exists('unsetByValue')) {
    function unsetByValue($array, $deletedValue): array {
        $array = !empty($array) ? $array : [];
        if (($key = array_search($deletedValue, $array)) !== false) unset($array[$key]);
        return $array;
    }
}

if (!function_exists('numericKeyToIntArr')) {
    function numericKeyToIntArr($array): array {
        $newArr = [];
        foreach ($array as $element) {
            $newArr[] = $element;
        }
        return $newArr;
    }
}
