<?php
error_reporting(0);
include('functions.php');



$Nquestions = $_GET['Nquestions'];

$min = (int) $_GET['min'];
$max = (int) $_GET['max'];

$result = [];
for ($i = 0; $i < (int) $Nquestions; $i++) {
    array_push($result, generate($min, $max));
}
if ($max - $min < 3) {
    $result = 0;
}
echo json_encode($result);
