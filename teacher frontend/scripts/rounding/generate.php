<?php
error_reporting(0);
include('functions.php');


$level = $_GET['level'];
$Nquestions = $_GET['Nquestions'];

$result = [];
for ($i=0; $i < (int)$Nquestions; $i++) { 
    array_push($result,generate($level));
}
echo json_encode($result);
