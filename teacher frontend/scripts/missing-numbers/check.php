<?php
error_reporting(0);
include('functions.php');


$ans = $_GET['ans'];

$result = check($ans);
echo json_encode($result);
