<?php
error_reporting(1);

include('functions.php');

$generated = $_POST['generatedData'];
$quiz = $_POST['quizData'];

/**
 * Start The Transaction
*/
$saveToDB = insert($generated, $quiz);

if ($saveToDB) {
    echo json_encode(['code' => 200, 'msg'=> 'This exercise has been scheduled!']);
} else {
    echo json_encode(['code' => 401, 'msg'=> "Something went wrong"]);
}