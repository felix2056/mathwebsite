<?php

function getExercises()
{
    require '../database.php';

    // Create connection
    $conn = mysqli_connect($config['DB_HOST'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_DATABASE']);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    /* Start maths_quiz transaction */
    $sql = "SELECT * FROM maths_quiz_excercise_sets 
    INNER JOIN maths_quiz ON maths_quiz_excercise_sets.id_Maths_Quiz_FK = maths_quiz.id_Maths_Quiz 
    INNER JOIN maths_quiz_topics ON maths_quiz_excercise_sets.id_Maths_Quiz_Topics_FK = maths_quiz_topics.id_Maths_Topic";
    
    $query = mysqli_query($conn, $sql);
    
    $response = array();
    
    while($row = mysqli_fetch_assoc($query)){
        $response[] = $row;
    }

    return $response;
}

function getQuestions($exercise_id)
{
    require '../database.php';

    // Create connection
    $conn = mysqli_connect($config['DB_HOST'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_DATABASE']);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    /* Start maths_quiz transaction */
    $sql = "SELECT * FROM maths_quiz_questions WHERE id_Maths_Excercise_Sets_FK = '$exercise_id'";
    
    $query = mysqli_query($conn, $sql);
    $response = array();
    
    while($row = mysqli_fetch_assoc($query)){
        $response[] = $row;
    }

    return $response;
}

function getSingle($question_id)
{
    require '../database.php';

    // Create connection
    $conn = mysqli_connect($config['DB_HOST'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_DATABASE']);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    /* Start maths_quiz transaction */
    $sql = "SELECT `id_Maths_Questions`, `Question`, `Question_Topic` FROM maths_quiz_questions WHERE id_Maths_Questions = '$question_id' LIMIT 1";
    $query = mysqli_query($conn, $sql);
    $response = mysqli_fetch_assoc($query);
    
    if (!$response) {
        return false;
    }

    //Tweak question and convert to accurate result for each quiz type
    if ($response['Question_Topic'] == 'missing-numbers' || $response['Question_Topic'] == 'order-numbers') {
        $response['Question'] = json_decode($response['Question']);   
    }

    /* Check if question has been answered */
    $checkAnsweredSql = "SELECT `Pupils_Answer` FROM maths_quiz_pupils_answers WHERE id_Maths_Quiz_Question_FK = '$question_id' LIMIT 1";
    $checkAnsweredQuery = mysqli_query($conn, $checkAnsweredSql);

    $pupilsAnswer = mysqli_fetch_assoc($checkAnsweredQuery);

    //First set pupil's answer to an empty string then modify if answer exists
    $response['Pupils_Answer'] = "";
    
    if (count($pupilsAnswer) > 0) {
        /*
        * Get answer for single and non-array results
        * - basic-operations
        * - writing-numbers
        * - money-counting
        * - roman-numbers
        */
        $response['Pupils_Answer'] = $pupilsAnswer['Pupils_Answer'];
        
        
        /*
        * Check by topic type and convert to array values if matched
        * - missing-numbers
        * - writing-numbers
        * - order-numbers
        */
        if ($response['Question_Topic'] == 'missing-numbers' || $response['Question_Topic'] == 'order-numbers' || $response['Question_Topic'] == 'time') {
            $response['Pupils_Answer'] = json_decode($pupilsAnswer['Pupils_Answer']);
        }
    }

    mysqli_close($conn);

    return $response;
}

function submitAnswer($answer, $question_id, $question_topic)
{
    require '../database.php';

    // Create connection
    $conn = mysqli_connect($config['DB_HOST'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_DATABASE']);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    /* Check if student answer is correct */
    $checksql = "SELECT `Answer` FROM maths_quiz_questions WHERE id_Maths_Questions = '$question_id' LIMIT 1";
    $checkquery = mysqli_query($conn, $checksql);
    if (!$checkquery) {
        return false;
    }

    $correctAns = mysqli_fetch_assoc($checkquery);


    //Check quiz type by the question topic
    if ($question_topic == "basic-operations" || $question_topic == "rounding" || $question_topic == "HCF" || $question_topic == "LCM" || $question_topic == "money-conversion") {
        $isCorrect = $answer == $correctAns['Answer'];
    } elseif ($question_topic == "algebra-word-problems" ) {
        //Decode correct answer and check if submitted answer matches any data in the correct answer array
        $correctAns['Answer'] = json_decode($correctAns['Answer']);

        for ($i=0; $i < count($correctAns['Answer']); $i++) { 
            if ($answer == $correctAns['Answer'][$i]) {
                $isCorrect = true;
            }
        }
    } elseif ($question_topic == "missing-numbers" || $question_topic == "order-numbers" || $question_topic == "time") {
        $answer = json_encode(explode(',', $answer));
        $isCorrect = $answer == $correctAns['Answer'];
    } elseif (
        $question_topic == "writing-numbers" || 
        $question_topic == "money-counting" || 
        $question_topic == "roman-numbers"
    ) {
        $answer = $answer[0];
        $isCorrect = $answer == $correctAns['Answer'];
    }

    // echo json_encode(['code' => 200, 'msg' => $answer]);
    // return;
    
    /* Check if already answered and then update */
    $checkAnsweredSql = "SELECT `id_Maths_Quiz_Question_FK` FROM maths_quiz_pupils_answers WHERE id_Maths_Quiz_Question_FK = '$question_id' LIMIT 1";
    $checkAnsweredQuery = mysqli_query($conn, $checkAnsweredSql);

    if (count(mysqli_fetch_assoc($checkAnsweredQuery)) > 0) {
        /* Update student answer to database */
        $sql = "UPDATE maths_quiz_pupils_answers SET Pupils_Answer = '$answer', Is_Correct = '$isCorrect' WHERE id_Maths_Quiz_Question_FK = '$question_id'";

        if (!mysqli_query($conn, $sql)) {
            return false;
        }
    } else {
        /* Insert student answer to database */
        $sql = "INSERT INTO maths_quiz_pupils_answers (id_Maths_Quiz_Question_FK, Pupils_Answer, Is_Correct) 
        VALUES ('$question_id', '$answer', '$isCorrect')";

        if (!mysqli_query($conn, $sql)) {
            return false;
        }
    }
    

    mysqli_close($conn);

    return 'Submitted successfully';
}
?>