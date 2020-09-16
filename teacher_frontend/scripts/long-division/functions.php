<?php
function check($ans, $r, $D, $d)
{
    $answer = '';
    if (floor($D / $d) == $ans && $D % $d == $r) {
        $correct = true;
        $answer = "";
    } else {
        $correct = false;
        $answer = divide($D, $d);
    }
    return json_encode(array(
        "correct" => $correct,
        'answer' => $answer
    ));
}

function generate($L, $r)
{
    if ($L == 'easy') {
        $d = rand(2, 8);
        $D = rand($d, 12);
    } elseif ($L == 'normal') {
        $d = rand(2, 12);
        $D = rand($d, 25);
    } elseif ($L == 'advance') {
        $d = rand(10, 50);
        $D = rand(100, 999);
    }
    if ($r == 'false') {
        $D = $D  * $d;
    }
    return array(
        "D" => $D,
        "d" => $d
    );
}

function divide($D, $d)
{
    $D = ((string) $D);
    $length = strlen($D);
    $n = "";
    $ans = array();
    $result = array();
    $step = 0;
    while (strlen($D) > 0) {
        $n = $n . $D[0];
        $D = substr($D, 1, strlen($D));
        array_push($ans, (string) (floor((int) $n / $d)));
        $x = floor((int) $n / $d) * $d;
        if ($x > 0) {
            $n = (string) ((int) $n % $d);

            try {
                //echo "_" . (string) $x . "_" . $n . $D[0] . "</br>";
                array_push($result, str_repeat(" ", $step + 1 - strlen((string) $x)) . (string) $x . str_repeat(" ", $length - $step - 1));
                array_push($result, str_repeat(" ", $step + 2 - strlen(($n . $D[0]))) . $n . $D[0] . str_repeat(" ", $length - $step - 2));
            } catch (Exception $e) {
                //echo "_" . (string) $x . "_" . $n . "</br>";
                //array_push($result, (string) $x, $n . "5");
            }
        }
        $step++;
    }
    return json_encode(array(
        "ans" => $ans,
        "result" => $result
    ));
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

    echo json_encode(['code' => 200, 'msg'=> $data]);

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
        
        $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Solution, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '123', '656', 'missing-numbers')";
        
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