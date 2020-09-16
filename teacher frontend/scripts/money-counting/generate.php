<?php
error_reporting(0);
include('functions.php');



$Nquestions = $_GET['Nquestions'];
$level = $_GET['level'];

$result = [];
for ($i = 0; $i < (int) $Nquestions; $i++) {
    array_push($result, generate($level));
}
echo json_encode($result);
