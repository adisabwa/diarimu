<?php 
function setStatusColor($value) {
    // Generate random values for each RGB component
    $r = rand(0, 255); // Red
    $g = rand(0, 255); // Green
    $b = rand(0, 255); // Blue
    
    // Return RGB format
    return "rgb($r, $g, $b)";
}