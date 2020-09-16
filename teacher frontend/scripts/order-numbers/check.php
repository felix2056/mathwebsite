<?php
error_reporting(0);
include('functions.php');


$ans = $_GET['ans'];
$cans = $_GET['cans'];

$result = check($ans,$cans);
echo json_encode($result);
