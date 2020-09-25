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

    while ($row = mysqli_fetch_assoc($query)) {
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

    $index = 0;

    while ($row = mysqli_fetch_assoc($query)) {
        /* Check if question has been answered */
        $checkAnsweredSql = "SELECT `Pupils_Answer`, `Is_Correct`, `Graded` FROM maths_quiz_pupils_answers WHERE id_Maths_Quiz_Question_FK = '$row[id_Maths_Questions]' LIMIT 1";
        $checkAnsweredQuery = mysqli_query($conn, $checkAnsweredSql);

        $pupilsAnswer = mysqli_fetch_assoc($checkAnsweredQuery);

        /**
         * First set pupil's answer to an empty string then modify if answer exists
         * Next set question status to an empty string and resolve if student has answered
         * Next set question grade to an empty string and resolve if answer correct or incorrect
        **/
        
        $row['Pupils_Answer'] = "";
        $row['Question_Status'] = "";
        $row['Question_Grade'] = "";

        if (count($pupilsAnswer) > 0) {
            $row['Pupils_Answer'] = parsePupilsAnswer($row['Question_Topic'], $pupilsAnswer['Pupils_Answer']);
            $row['Question_Status'] = "answered";

            if ($pupilsAnswer['Graded'] == 1) {
                if ($pupilsAnswer['Is_Correct'] == 1) {
                    $row['Question_Grade'] = 'tick';
                } else {
                    $row['Question_Grade'] = 'close';
                }
            }
        }

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
    $sql = "SELECT `id_Maths_Questions`, `Question`, `Question_Topic`, `Answer`, `Solution` FROM maths_quiz_questions WHERE id_Maths_Questions = '$question_id' LIMIT 1";
    $query = mysqli_query($conn, $sql);
    $response = mysqli_fetch_assoc($query);

    if (!$response) {
        return false;
    }

    //Tweak question and solution and convert to accurate data for each quiz type
    $response['Question'] = parseQuestion($response['Question_Topic'], $response['Question']);
    $response['Solution'] = parseSolution($response['Question_Topic'], $response['Solution']);
    

    /* Check if question has been answered */
    $checkAnsweredSql = "SELECT `Pupils_Answer`, `Is_Correct`, `Graded` FROM maths_quiz_pupils_answers WHERE id_Maths_Quiz_Question_FK = '$question_id' LIMIT 1";
    $checkAnsweredQuery = mysqli_query($conn, $checkAnsweredSql);

    $pupilsAnswer = mysqli_fetch_assoc($checkAnsweredQuery);

    //First set pupil's answer to an empty string then modify if answer exists
    $response['Graded'] = $pupilsAnswer['Graded'];
    $response['Pupils_Answer'] = "";
    $response['Question_Grade'] = "";

    if (count($pupilsAnswer) > 0) {
        $response['Pupils_Answer'] = parsePupilsAnswer($response['Question_Topic'], $pupilsAnswer['Pupils_Answer']);
        
        if ($pupilsAnswer['Graded'] == 1) {
            if ($pupilsAnswer['Is_Correct'] == 1) {
                $response['Question_Grade'] = 'tick';
            } else {
                $response['Question_Grade'] = 'close';
            }
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
    $checksql = "SELECT `Answer` FROM maths_quiz_questions WHERE id_Maths_Questions = '$question_id'";
    $checkquery = mysqli_query($conn, $checksql);
    if (!$checkquery) {
        return false;
    }

    $correctAns = mysqli_fetch_assoc($checkquery);
    
    //parse answer to match all quiz types and return the correct format
    $correctAns['Answer'] = parseAnswer($question_topic, $correctAns['Answer']);

    $isCorrect = $answer == $correctAns['Answer'];

    // echo 'answer = ' . $answer . ' ' . ' correct answer = ' .$correctAns['Answer'] . 'isCorrect = ' .$isCorrect;
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

function getHasAnsweredAll($exercise_id) {
    require '../database.php';

    // Create connection
    $conn = mysqli_connect($config['DB_HOST'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_DATABASE']);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    //Check if student has answered all questions in this exercise
    $sql = "SELECT * FROM `maths_quiz_questions` AS q LEFT JOIN maths_quiz_pupils_answers ON q.`id_Maths_Questions` = maths_quiz_pupils_answers.`id_Maths_Quiz_Question_FK` WHERE `id_Maths_Excercise_Sets_FK` = '$exercise_id' AND `id_Pupils_Answers` IS NULL";
    $query = mysqli_query($conn, $sql);

    if (count(mysqli_fetch_assoc($query)) > 0) {
        //There are still unanswered questions so return false
        return false;
    }

    return true;
}

function getMyGrade($exercise_id) {
    require '../database.php';

    // Create connection
    $conn = mysqli_connect($config['DB_HOST'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_DATABASE']);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    //Check if student has answered all questions in this exercise
    $sql = "SELECT * FROM maths_quiz_questions WHERE id_Maths_Excercise_Sets_FK = '$exercise_id'";
    $query = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($query)) {
        /* Check if question has been answered */
        $answerSql = "UPDATE maths_quiz_pupils_answers SET Graded = 1 WHERE id_Maths_Quiz_Question_FK = '$row[id_Maths_Questions]'";
        
        if (!mysqli_query($conn, $answerSql)) {
            return false;
        }
    }

    mysqli_close($conn);
    return "Quiz Graded! Review your results";
}

function parseQuestion($question_topic, $question) {
    if ($question_topic == 'missing-numbers' || $question_topic == 'order-numbers' || $question_topic == 'rounding' || $question_topic == 'long-division' || $question_topic == 'place-value') {
        $question = json_decode($question);
    }

    return $question;
}

function parseSolution($question_topic, $solution) {
    if ($question_topic == 'long-division') {
        $solution = json_decode($solution);
    }

    return $solution;
}

function parseAnswer($question_topic, $answer) {
    //First encode correct answer as JSON to match the submitted JSON answer by student
    $answer = json_encode($answer);

    //Then check quiz type by the question topic and parse answer correctly
    if ($question_topic == "missing-numbers" || $question_topic == "order-numbers") {
        $answer = json_encode(explode(',', $answer));
    } elseif ($question_topic == "rearrange-formula" || $question_topic == "place-value-as-words" || $question_topic == "place-value" || $question_topic == "long-division" || $question_topic == "time" || $question_topic == "shopping-problems") { 
        $answer = json_decode($answer);
    } elseif ($question_topic == "algebra-word-problems") {
        $ans = json_decode(json_decode($answer));
        $answer = json_encode($ans[0]);
    }

    return $answer;
}

function parsePupilsAnswer($question_topic, $answer) {
    $answer = json_decode($answer);
    /*
    * Check by topic type and convert to array values if matched
    * - missing-numbers
    * - writing-numbers
    * - order-numbers
    */
    // if ($question_topic == 'missing-numbers' || $question_topic == 'order-numbers' || $question_topic == 'time' || $question_topic == 'long-division' || $question_topic == 'place-value-as-words') {
    //     $answer = json_decode($answer);
    // }

    return $answer;
}



/*if ($question_topic == "basic-operations" || $question_topic == "rounding" || $question_topic == "HCF" || $question_topic == "LCM" || $question_topic == "money-conversion") {
    $isCorrect = $answer == $correctAns['Answer'];
} elseif ($question_topic == "algebra-word-problems") {
    //Decode correct answer and check if submitted answer matches any data in the correct answer array
    $correctAns['Answer'] = json_decode($correctAns['Answer']);

    for ($i = 0; $i < count($correctAns['Answer']); $i++) {
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
}*/