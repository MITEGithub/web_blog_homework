<?php
function generateToken($length = 64) {
    return bin2hex(random_bytes($length / 2));  
}
?>

