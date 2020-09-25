<?php

function generate($L, $type)
{
    $questions = [];
    $answer = [];
    if ($L == 'easy') {
        $n1 = rand(2, 10);
        $n2 = rand(3, 10);
        $sub = rand(1, $n2 - 1);
        $devide = pow(10, strlen(strval(min($n1, $n1))));
        $n3 = $n1 * $n2;
    } elseif ('normal') {
        $n1 = rand(10, 100);
        $n2 = rand(10, 100);
        $sub = rand(1, $n2 - 1);
        $devide = pow(10, strlen(strval(min($n1, $n1))) - 1);
        $n3 = $n1 * $n2;
    } elseif ('advance') {
        $n1 = rand(100, 1000);
        $n2 = rand(100, 1000);
        $devide = pow(10, strlen(strval(min($n1, $n1))) - 1);
        $n3 = $n1 * $n2;
    }


    $question = '
    <p><input type="text" class="form-control" placeholder="' . $n3 . '" disabled><span>&divide;</span><input type="text" class="form-control" placeholder="' . $n1 . '" disabled><span>=</span><input type="text" class="form-control" placeholder="' . $n2 . '" disabled></p>
    <p><strong>Without doing any calculation</strong>, write down the missing numbers in the empty boxes below.</p>';

    $qArr = [
        '<p><input type="text" class="form-control" placeholder="' . $n3 . '" disabled><span>&divide;</span><input type="text" class="form-control" placeholder="' . $n2 . '" disabled><span>=<q1><btn1> </p>',
        '<p><input type="text" class="form-control" placeholder="' . ($n1 / $devide) . '" disabled><span>&times;</span><q2><span>=</span><input type="text" class="form-control" placeholder="' . ($n3 / $devide / $devide) . '" disabled><btn2></p>',
        '<p><input type="text" class="form-control" placeholder="' . $n1 . '" disabled><span>&times;</span><input type="text" class="form-control" placeholder="' . $n2 . '" disabled><span>=<q4><btn4></p>'
    ];
    $q3 = '<p><input type="text" class="form-control" placeholder="' . $n1 . '" disabled><span>&times;</span><q3><span>=</span><input type="text" class="form-control" placeholder="' . $n3 . '" disabled><span>-</span><input type="text" class="form-control" placeholder="' . $n1 . '" disabled><btn3></p>';
    shuffle($qArr);
    $question = $question . $qArr[0] . $qArr[1] . $q3;
    array_push($questions, $question);
    $answer = [$n1, $n2 / $devide, $n2 - 1,$n3];
    $question = $questions[rand(0, count($questions) - 1)];

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
        //$answer = json_encode($data[$i]['a']);
        
        $answer = json_encode(array_slice($data[$i]['a'], 1));
        
        $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '100', 'rearrange-formula')";
        
        if (!mysqli_query($conn, $query)) {
            return false;
        }
    }

    return true;
}
