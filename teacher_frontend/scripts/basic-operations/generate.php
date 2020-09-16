<?php
error_reporting(0);
include('functions.php');


$operation = $_GET['operation'];
$Nquestions = $_GET['Nquestions'];
$f1 = $_GET['f1'];
$f2 = $_GET['f2'];

$result = [];
for ($i=0; $i < (int)$Nquestions; $i++) { 
    array_push($result,generate($operation,$f1,$f2));
}
echo json_encode($result);
