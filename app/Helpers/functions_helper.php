<?php

function excelColumnRange($lower, $upper) {
    ++$upper;
    $result = [];
    for ($i = $lower; $i !== $upper; ++$i) {
        $result[] = $i;
    }
    return $result;
}

function setRandomColor() {
    // Generate random values for each RGB component
    $r = rand(0, 255); // Red
    $g = rand(0, 255); // Green
    $b = rand(0, 255); // Blue
    
    // Return RGB format
    return "rgb($r, $g, $b)";
}

function toNumber($number){
    return preg_replace("/[^0-9]/", '', $number);
}
