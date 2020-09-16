<?php
error_reporting(1);
include('functions.php');

$exercises = getExercises();

echo json_encode($exercises);