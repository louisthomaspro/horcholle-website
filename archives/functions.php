<?php



// Return array of $b items not in $a
function flip_isset_diff($b, $a)
{ // very little bit faster
    $at = array_flip($a);
    $d = array();
    foreach ($b as $i)
        if (!isset($at[$i]))
            $d[] = $i;
    return $d;
}

// Map each $item in $array with $item[$key] key value
function map_with($array, $key)
{
    $array_with_keys = array_fill_keys(self::extract_key($array, $key), 'test');
    for ($i = 0; $i < count($array); $i++) {
        $key_value = $array[$i][$key];
        $array_with_keys[$key_value] = $array[$i];
    }
    return $array_with_keys;
}

// Extract all $item[$key] values from $array
function extract_key($array, $key)
{
    $new_array = array();
    foreach ($array as $item) {
        array_push($new_array, $item[$key]);
    }
    return $new_array;
}
