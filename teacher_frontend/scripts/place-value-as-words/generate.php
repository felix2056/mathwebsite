<?php
error_reporting(0);
include('functions.php');



$Nquestions = $_GET['Nquestions'];
$qt = $_GET['question-type'];
$qtm = $_GET['question-type_mode'];

$result = [];
for ($i = 0; $i < (int) $Nquestions; $i++) {
    array_push($result, generate($qtm,$qt));
}
echo json_encode($result);
