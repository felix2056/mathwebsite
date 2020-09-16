<?php
error_reporting(0);
include('functions.php');



$Nquestions = $_GET['Nquestions'];
$qt = $_GET['question-type'];
$min = $_GET['min'];
$max = $_GET['max'];

$result = [];
for ($i = 0; $i < (int) $Nquestions; $i++) {
    array_push($result, generate($min,$max,$qt));
}
echo json_encode($result);
