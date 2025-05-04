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
    return (int) preg_replace("/[^0-9]/", '', $number);
}


function toPhoneNumber($number){
    preg_match('/^0+/', $number, $matches);
    $number = toNumber($number);
    // var_dump($matches, $number);
    $hp = toNumber($number);
    return ($matches[0] ?? '').$hp;
}