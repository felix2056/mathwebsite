<?php

function generate($qtm, $qt)
{
    if ($qt == "TU") {
        $number = rand(1, 99);
    } else if ($qt == "HTU") {
        $number = rand(100, 999);
    } else if ($qt == "THTU") {
        $number = rand(1000, 9999);
    } else if ($qt == "TTTHTU") {
        $number = rand(10000, 99999);
    }
    $number_ = $number;
    $question = "";
    $placeNameList = ["Units","Tens","Hundreds","Thousands","Ten Thousands"];
    $index = 0;
    $answer=[];
    if ($qtm == "WN") {
        while ((int)$number !=0) {
            if($index == 0){
                $question = '<li><q'.($index+1).'>'.$placeNameList[$index].'</li>' . $question;
            }else{
                $question = '<li><q'.($index+1).'>'.$placeNameList[$index].'<span>&plus;</span></li>' . $question;
            }
            array_push($answer,$number%10);
            $number = $number/10;
            $index+=1;
        }
        $question = '<ul class="N2W"> <li>' . $number_ . ' <span>&equals;</span></li>' . $question . '<chbtn></ul>';
    }else{
        while ((int)$number !=0) {
            if($index == 0){
                $question = '<li><span>'.($number%10).'</span>'.$placeNameList[$index].'</li>' . $question;
            }else{
                $question = '<li><span>'.($number%10).'</span>'.$placeNameList[$index].'<span>&plus;</span></li>' . $question;
            }
            $number = $number/10;
            $index+=1;
        }
        $answer = [$number_];
        $question = '<ul class="W2N">' . $question . '<q1><chbtn></ul>';
    }
    return array(
        'q' => $question,
        'a' => $answer
    );
}

function check($ans, $cans)
{
    if (round($ans, 2) == round($cans, 2)) {
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
        $answer = json_encode($data[$i]['a']);
        
        $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '100', 'place-value-as-words')";
        
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
