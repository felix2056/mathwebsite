<?php

function generate($min, $max,$qt)
{
    $number = rand($min,$max);
    $rnumber = romanize($number);
   if($qt=="rm"){
    $question = '<div><span>'.$rnumber.'</span><span>=</span><q1></div>';
    $answer = [$number];
   }else{ 
       $question = '<div><span>'.$number.'</span><span>=</span><q1></div>';
       $answer = [$rnumber];

   }
    return array(
        'q' => $question,
        'a' => $answer
    );
}


function romanize($number)
{
	$numerals = array( "M"=>1000, "CM"=>900, "D"=>500, "CD"=>400, "C"=>100, "XC"=>90, "L"=>50, "XL"=>40, "X"=>10, "IX"=>9, "V"=>5, "IV"=>4, "I"=>1 );	
	$result = "";
	foreach ($numerals as $key=>$value) {
		$result .= str_repeat($key, $number / $value);
		$number %= $value;
	}
	return $result;
}




function check($ans, $cans)
{
    if (strtolower($ans) == strtolower($cans)) {
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
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '123', '656', 'roman-numbers')";
            if (!mysqli_query($conn, $query)) {
                return false;
            }
        }
    }

    return true;
}