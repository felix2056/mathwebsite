<?php 
error_reporting(1);
include('functions.php');

$exercise_id = $_GET['exercise_id'];

$grades = getMyGrade($exercise_id);

echo json_encode($grades);