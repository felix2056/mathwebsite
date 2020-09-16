<?php
error_reporting(1);
include('functions.php');

//$ans = explode(',', $_POST['ans']);

$ans = $_POST['ans'];
$question_id = $_POST['question_id'];
$question_topic = $_POST['question_topic'];

// echo json_encode($ans);
// return;

$answer = submitAnswer($ans, $question_id, $question_topic);

echo json_encode($answer);