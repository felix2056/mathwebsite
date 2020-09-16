<?php
error_reporting(0);
include('functions.php');



$Nquestions = $_GET['Nquestions'];
$operation = $_GET['operation'];
$level = $_GET['level'];

$result = [];
for ($i = 0; $i < (int) $Nquestions; $i++) {
    array_push($result, generate($level,$operation));
}
echo json_encode($result);
