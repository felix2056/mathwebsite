<?php

function generate($type)
{
   if($type=="mixed"){
      $type = ['SM','MH','MT','AMPM'][rand(0,3)];
   }
   if ($type == "SM") {

      if (rand(0, 2) == 1) {
         $MIN = rand(1, 120);
         $question = '<div class="input-group-wrapper">
         <div class="input-group mr-2">
            <input type="number" class="form-control" placeholder="' . $MIN . '" disabled>
            <div class="input-group-append">
               <div class="input-group-text">mins</div>
            </div>
         </div>
         <span><strong>=</strong></span>
         <q1>';
         $answer = [($MIN * 60) . " secs"];
      } else {
         $MIN = rand(1, 25);
         $question = '<div class="input-group-wrapper">
         <div class="input-group mr-2">
            <input type="number" class="form-control" placeholder="' . ($MIN * 60) . '" disabled>
            <div class="input-group-append">
               <div class="input-group-text">secs</div>
            </div>
         </div>
         <span><strong>=</strong></span>
         <q1>';
         $answer = ["$MIN mins"];
      }
   } else if ($type == "MH") {
      $MIN = rand(1, 500);
      $hours = floor($MIN / 60);
      $minutes = ($MIN % 60);
      if (rand(0, 1) == 1) {
         $question = '<div class="input-group-wrapper">
         <div class="input-group mr-2">
            <input type="number" class="form-control" placeholder="' . $MIN . '" disabled>
            <div class="input-group-append">
               <div class="input-group-text">mins</div>
            </div>
         </div>
         <span><strong>=</strong></span>
         <q1>';
         $answer = ["$hours hours", "$minutes mins"];
      } else {
         $question = '<div class="input-group-wrapper">
         <div class="input-group mr-2">
            <input type="number" class="form-control" placeholder="' . $hours . '" disabled>
            <div class="input-group-append">
               <div class="input-group-text">hours</div>
            </div>
         </div>

         <div class="input-group mr-2">
            <input type="number" class="form-control" placeholder="' . $minutes . '" disabled>
            <div class="input-group-append">
               <div class="input-group-text">mins</div>
            </div>
         </div>

         <span><strong>=</strong></span>
         <q1>';
         $answer = ["$MIN mins"];
      }
   } elseif ($type == "MT") {
      $timestamp = mt_rand(1, time());

      $time_12 = date("h:i A", $timestamp);
      $time_24 = date("Hi", $timestamp);
      $question = '<div class="input-group-wrapper">
         <div class="input-group mr-2">
            <input type="number" class="form-control" placeholder="' . (explode(" ",$time_12)[0]). '" disabled>
            <div class="input-group-append">
               <div class="input-group-text">' .(explode(" ",$time_12)[1]). '</div>
            </div>
         </div>
         <span><strong>=</strong></span>
         <q1>';
      $answer = ["$time_24 hours"];
   }else if($type == "AMPM"){
      $timestamp = mt_rand(1, time());
      $time_12 = date("h:i A", $timestamp);
      $time_24 = date("Hi", $timestamp);
      $question = '<div class="input-group-wrapper">
         <div class="input-group mr-2">
            <input type="number" class="form-control" placeholder="' . $time_24 . '" disabled>
            <div class="input-group-append">
               <div class="input-group-text">Hours</div>
            </div>
         </div>
         <span><strong>=</strong></span>
         <op-ch>';
      $answer = ["$time_12"];
   }

   return array(
      'q' => $question,
      'a' => $answer
   );
}

function convertToHoursMins($time)
{
   if ($time < 1) {
      return;
   }
   $hours = floor($time / 60);
   $minutes = ($time % 60);
   if ($hours == 0) {
      return sprintf('%02d mins', $minutes);
   } else {
      return sprintf('%02d hours %02d mins', $hours, $minutes);
   }
}
function convertToHoursMins2($time)
{
   return sprintf('%02d min', $time);
}

function check($ans, $cans)
{
   if ($ans == $cans) {
      return true;
   } else {
      return false;
   }
}

function insert($data, $info)
{
    require '../../database.php';

    // Create connection
    $conn = mysqli_connect($config['DB_HOST'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_DATABASE']);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // echo json_encode(['code' => 200, 'msg'=> $data]);
    // return;

    /* Start maths_quiz transaction */
    $id_Teachers_FK = 1;
    $Status = 'Scheduled';
    $Created = date("Y-m-d H:i:s");
    $Updated = date("Y-m-d H:i:s");

    $maths_quiz_sql = "INSERT INTO maths_quiz (id_Teachers_FK, Start_Date, End_Date, Status, Created, Updated) 
    VALUES ('$id_Teachers_FK', '$info[start_date]', '$info[end_date]', '$Status', '$Created', '$Updated')";

    if (!mysqli_query($conn, $maths_quiz_sql)) {
        return false;
    }
    $info['maths_quiz_last_id'] = mysqli_insert_id($conn);


    /* Start maths_quiz_topics transaction */
    $maths_quiz_topics_sql = "INSERT INTO maths_quiz_topics (Maths_Topic, Maths_Topic_Instruction) 
    VALUES ('$info[topic]', '$info[instruction]')";

    if (!mysqli_query($conn, $maths_quiz_topics_sql)) {
        // echo "Error: " . $maths_quiz_topics_sql . "<br>" . mysqli_error($conn);
        return false;
    }
    $info['maths_quiz_topics_last_id'] = mysqli_insert_id($conn);

    /* Start maths_quiz_excercise_sets transaction */
    $maths_quiz_excercise_sets_sql = "INSERT INTO maths_quiz_excercise_sets (id_Maths_Quiz_FK, id_Maths_Quiz_Topics_FK, Num_Questions, Total_Marks_Available) 
    VALUES ('$info[maths_quiz_last_id]', '$info[maths_quiz_topics_last_id]', '$info[Nquestions]', '$info[total_marks]')";

    if (!mysqli_query($conn, $maths_quiz_excercise_sets_sql)) {
        return false;
    }
    $info['maths_quiz_excercise_sets_last_id'] = mysqli_insert_id($conn);

    /* Start maths_quiz_questions transaction */
    if(is_array($data)){
        foreach ($data as $record) {
            $question = $record['q'];
            $answer = $record['a'][0];

            $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '100', 'time-conversion')";
            if (!mysqli_query($conn, $query)) {
                return false;
            }
        }
    }

    return true;
}
