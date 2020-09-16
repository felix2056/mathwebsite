<?php

function generate($min, $max)
{
    $n0 = rand(1, 12);
    $n1 = rand($min, $max);
    $n2 = rand($min, $max);

    $n1 = floor($n1 / $n0) * $n0;
    $n2 = floor($n2 / $n0) * $n0;

    $factors1 = factors($n1);
    $factors2 = factors($n2);

    $question = '<p><span>' . $n1 . '</span><span class="line" id="ID1" ans="'.$factors1.'"></span></p>
    <p><span>' . $n2 . '</span><span class="line"  id="ID2" ans="'.$factors2.'"></span></p>
    <p><span>Ans:</span><q1></p>';
    $answer = [gcd($n1, $n2)];
    return array(
        'q' => $question,
        'a' => $answer
    );
}

function factors($n){
    $fArry = [];
    for ($i=1; $i <= $n; $i++) { 
       if($n%$i==0){
           array_push($fArry,$i);
       }
    }
    return implode(",",$fArry);
}
function gcd($a, $b)
{
    if ($b == 0) {
        return $a;
    } else {
        return gcd($b, $a % $b);
    }
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

            $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Solution, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '123', '656', 'HCF')";
            if (!mysqli_query($conn, $query)) {
                return false;
            }
        }
    }

    return true;
}
