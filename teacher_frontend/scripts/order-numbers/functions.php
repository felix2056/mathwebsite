<?php

function generate($min, $max)
{
    $NoOfN = 4;
    $NoOfN = $NoOfN;

    $q = range($min, $max);
    $q_arr_idx = array_rand($q, $NoOfN);
    $q_arr = [];
    foreach ($q_arr_idx as $idx) {
        array_push($q_arr, $q[$idx]);
    }
    $c_ans = $q_arr;
    sort($c_ans);
    shuffle($q_arr);

    return array(
        'q' => $q_arr,
        'ans' => $c_ans
    );
}

function check($ans, $cans)
{
    $c =  (int) $ans[0];
    $answer = true;
    for ($i = 0; $i < count($ans); $i++) {
        if ($cans[$i] != (int) $ans[$i]) {
            $answer = false;
        }
        $c = $c + 1;
    }
    return $answer;
}

function miss($miss, $q_arr)
{
    if (is_int($miss)) {
        $q_arr[$miss] = '';
        return $q_arr;
    }
    foreach ($miss as $i) {
        # code...
        $q_arr[$i] = '';
    }
    return $q_arr;
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
    for ($i = 0; $i < count($data); $i++) {
        $question = json_encode($data[$i]['q']);
        $answer = json_encode($data[$i]['ans']);
        
        $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '100', 'order-numbers')";
        
        if (!mysqli_query($conn, $query)) {
            return false;
        }
    }
    // if(is_array($data)){
    //     foreach ($data as $record) {
    //         $question = serialize($record['q']);
    //         $answer = serialize($record['ans']);

    //         $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Solution, Question_Weight, Question_Topic) 
    //         VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '123', '656', 'missing-numbers')";
    //         if (!mysqli_query($conn, $query)) {
    //             return false;
    //         }
    //     }
    // }

    return true;
}