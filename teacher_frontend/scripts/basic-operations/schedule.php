<?php
error_reporting(1);

include('functions.php');

$generated = $_POST['generatedData'];
$quiz = $_POST['quizData'];

// $new_array = [];

// foreach ($generated as $record) {
//     foreach ($record as $row) {
//         array_push($new_array, $row);   
//     }
// }

// echo json_encode(['code' => 200, 'msg'=> $generated[0]['ans']]);
// return;

/**
 * Start The Transaction
*/
$saveToDB = insert($generated, $quiz);

if ($saveToDB) {
    echo json_encode(['code' => 200, 'msg'=> 'This exercise has been scheduled!']);
} else {
    echo json_encode(['code' => 401, 'msg'=> "Something went wrong"]);
}