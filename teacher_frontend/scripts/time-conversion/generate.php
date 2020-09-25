<?php
error_reporting(0);
include('functions.php');



$Nquestions = $_GET['Nquestions'];
$type= $_GET['type'];


$result = [];
for ($i = 0; $i < (int) $Nquestions; $i++) {
    array_push($result, generate($type));
}
echo json_encode($result);
