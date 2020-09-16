<?php
function generate($opr, $f1, $f2)
{
    $n1 = rand(pow(10, $f1 - 1), pow(10, $f1) - 1);
    $n2 = rand(pow(10, $f2 - 1), pow(10, $f2) - 1);
    if ($opr == 'plus') {
        $q = "<p><span>&plus;</span><span>$n1</span><br /><span>$n2</span></p>";
        $a = $n1 + $n2;
    } else if ($opr == 'minus') {
        $n1 = rand(pow(10, $f1 - 1), pow(10, $f1) - 1);
        $n2 = rand(pow(10, $f2 - 1), min($n1, pow(10, $f2) - 1));
        $q = "<p><span>&minus;</span><span>$n1</span><br /><span>$n2</span></p>";
        $a = $n1 - $n2;
    } else if ($opr == 'multiply') {
        $q = "<p><span>&times;</span><span>$n1</span><br /><span>$n2</span></p>";
        $a = $n1 * $n2;
    } else if ($opr == 'divide') {
        $n1 = rand(pow(10, $f1 - 1), pow(10, $f1) - 1);
        $n2 = rand(pow(10, $f2 - 1), min($n1, pow(10, $f2) - 1));

        $a = $n1 / $n2;
        $a = (int) $a;
        $n1 = $a * $n2;
        $q = "<p><span>&divide;</span><span>$n1</span><br /><span>$n2</span></p>";
        $a = $n1 / $n2;
    }
    return array(
        "ans" => round($a, 2),
        "q" => $q
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
            $answer = $record['ans'];

            $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Solution, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '123', '656', 'basic-operations')";
            if (!mysqli_query($conn, $query)) {
                return false;
            }
        }
    }

    return true;
}
