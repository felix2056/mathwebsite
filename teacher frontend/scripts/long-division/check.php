<?php
error_reporting(0);
include('functions.php');

$ans = $_GET['ans'];
$r = $_GET['rem'];
$D = $_GET['D'];
$d = $_GET['d'];

echo check($ans,$r,$D,$d);
