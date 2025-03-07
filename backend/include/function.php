<?php

function sanitizeInput($mysqli, $input)
{
    $data = $mysqli->real_escape_string($input);
    $data = trim($data);
    return $data;
}

?>