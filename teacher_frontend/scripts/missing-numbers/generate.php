<?php
error_reporting(0);
include('functions.php');


$level = $_GET['level'];
$Nquestions = $_GET['Nquestions'];
$show_f = $_GET['show_f'];
$show_c = $_GET['show_c'];
$min = (int)$_GET['min'];
$max = (int)$_GET['max'];

$result = [];
for ($i=0; $i < (int)$Nquestions; $i++) { 
    array_push($result,generate($level,$show_f,$show_c,$min,$max));
}
if($max-$min<10){
    $result = 0;
}
echo json_encode($result);
