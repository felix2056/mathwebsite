<?php
error_reporting(0);
include('functions.php');



$Nquestions = $_GET['Nquestions'];

$level = $_GET['level'];
$std =  $_GET['std'];
if($std=="all"){
    $result = [];
    for ($i = 0; $i < (int) $Nquestions; $i++) {
        array_push($result, generate_mix($level));
    }
    echo json_encode($result);
}else{
    $result = [];
    for ($i = 0; $i < (int) $Nquestions; $i++) {
        array_push($result, generate($level, $std));
    }
    echo json_encode($result);
}