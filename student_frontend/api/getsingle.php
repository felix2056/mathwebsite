<?php
error_reporting(1);
include('functions.php');

$question_id = $_GET['question_id'];

$question = getSingle($question_id);

echo json_encode($question);