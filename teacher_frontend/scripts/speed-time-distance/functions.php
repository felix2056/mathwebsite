<?php

function generate($L, $std)
{
    $myfile = fopen("db/db.json", "r") or die("Unable to open file!");
    $db = json_decode(fread($myfile, filesize("db/db.json")), true);
    fclose($myfile);
    $vehivle = $db[rand(0, count($db) - 1)];

    if ($L == 'easy') {
        $speed = rand($vehivle['speed_min'] / 10, $vehivle['speed_max'] / 10) * 10;
        $time = rand($vehivle['time_min'], $vehivle['time_max']);
    } elseif ($L == 'normal') {
        $speed = rand($vehivle['speed_min'], $vehivle['speed_max']);
        $time = rand($vehivle['time_min'], $vehivle['time_max']);
    } elseif ($L == 'advance') {
        $speed = rand($vehivle['speed_min'], $vehivle['speed_max']);
        $time = rand($vehivle['time_min'] * 4, $vehivle['time_max'] * 4) / 4;
    }
    $su_i = rand(0, 1);
    if ($su_i == 0) {
        $su = 'km/h';
        $du = 'km';
        $tu = 'hour(s)';
    } elseif ($su_i == 1) {
        $su = 'mph (miles per hour)';
        $du = 'mile(s)';
        $tu = 'hour(s)';
    }


    $distance = $speed * $time;
    if ($std == 'speed') {
        $question = '<p class="lead">' . $vehivle['prefix'] . " " . $vehivle['name'] . " " . $vehivle['verb'] . " <strong>" . $distance . ' ' . $du . "</strong> in <strong>" . $time . " " . $tu . "</strong></p>";
        $question = $question . '<p><strong>What is the ' . $vehivle['name'] . "'s average speed in " . $su . "</strong></p>";
        $answer = $speed;
        $eqn = 'speed = distance/time';
    } elseif ($std == 'distance') {
        $questions = [];

        $question = '<p class="lead">' . $vehivle['prefix'] . " " . $vehivle['name'] . " " . $vehivle['verb'] . "at speed of <strong>" . $speed . " " . $su . ".</strong> How far will it have travelled in <strong>" . $time . " " . $tu . "</strong>?</p>";
        array_push($questions, $question);

        $question = '<p class="lead">' . $vehivle['prefix'] . " " . $vehivle['name'] . " " . $vehivle['verb'] . " for <strong>" . $time . " " . $tu . "</strong> at a speed of <strong>" . $speed . ' ' . $su . ".</strong> What distance has it travelled?</p>";
        array_push($questions, $question);

        $question = '<p class="lead">' . $vehivle['prefix'] . " " . $vehivle['name'] . " " . $vehivle['verb'] . " at constant speed of <strong>" . $speed . " " . $su . ".</strong>  How far can it travel in <strong>" . $time . " " . $tu . "</strong>?</p>";
        array_push($questions, $question);
        array_push($questions, $question);

        $question = $questions[rand(0, count($question) + 1)];
        $answer = $distance;
        $eqn = 'distance = speed*time';
    } elseif ($std == 'time') {
        $questions = [];

        $question = '<p class="lead">How much time will it take ' . $vehivle['prefix'] . " " . $vehivle['name'] . " " . $vehivle['ing'] . " at <strong>" . $speed . " " . $su . "</strong> to travel a distance of <strong>" . $distance . " " . $du . "</strong>?</p>";
        array_push($questions, $question);

        $question = '<p class="lead">Find the time taken by ' . $vehivle['prefix'] . " " . $vehivle['name'] . " " . $vehivle['ing'] . " with an average speed of <strong>" . $speed . " " . $su . "</strong> to complete a journey of <strong>" . $distance . " " . $du . "</strong>?</p>";
        array_push($questions, $question);

        $question = '<p class="lead">' . $vehivle['prefix'] . " " . $vehivle['name'] . " " . $vehivle['verb'] . "<strong> " . $distance . " " . $du . "</strong> at a speed of <strong>" . $speed . " " . $su . ".</strong> How much time will it take to complete this journey??</p>";
        array_push($questions, $question);

        $question = $questions[rand(0, count($question) + 1)];

        $answer = $time;
        $eqn = 'time = distance/speed';
    }

    return array(
        'q' => $question,
        'a' => round($answer, 3),
        'equ' => $eqn
    );
}


function generate_mix($L)
{
    $myfile = fopen("db/db.json", "r") or die("Unable to open file!");
    $db = json_decode(fread($myfile, filesize("db/db.json")), true);
    fclose($myfile);
    $vehivle = $db[rand(0, count($db) - 1)];

    if ($L == 'easy') {
        $speed_1 = rand($vehivle['speed_min'] / 10, $vehivle['speed_max'] / 10) * 10;
        $time_1 = rand($vehivle['time_min'], $vehivle['time_max']);
        $speed_2 = rand($vehivle['speed_min'] / 10, $vehivle['speed_max'] / 10) * 10;
        $time_2 = rand($vehivle['time_min'], $vehivle['time_max']);
    } elseif ($L == 'normal') {
        $speed_1 = rand($vehivle['speed_min'], $vehivle['speed_max']);
        $time_1 = rand($vehivle['time_min'], $vehivle['time_max']);
        $speed_2 = rand($vehivle['speed_min'], $vehivle['speed_max']);
        $time_2 = rand($vehivle['time_min'], $vehivle['time_max']);
    } elseif ($L == 'advance') {
        $speed_1 = rand($vehivle['speed_min'], $vehivle['speed_max']);
        $time_1 = rand($vehivle['time_min'] * 4, $vehivle['time_max'] * 4) / 4;
        $speed_2 = rand($vehivle['speed_min'], $vehivle['speed_max']);
        $time_2 = rand($vehivle['time_min'] * 4, $vehivle['time_max'] * 4) / 4;
    }
    $su_i = rand(0, 1);
    if ($su_i == 0) {
        $su = 'km/h';
        $du = 'km';
        $tu = 'hour(s)';
    } elseif ($su_i == 1) {
        $su = 'mph (miles per hour)';
        $du = 'mile(s)';
        $tu = 'hour(s)';
    }


    $distance_1 = $speed_1 * $time_1;
    $distance_2 = $speed_2 * $time_2;

    $questions = [];

    $question = '<p class="lead">' . $vehivle['prefix'] . " " . $vehivle['name'] . " " . $vehivle['verb']  . " 
    a distance of <strong> " . $distance_1 . " " . $du . "</strong> at a speed of <strong>" . $speed_1 . " " . $su . ".</strong>

    and then for <strong> " . $distance_2 . " " . $du . "</strong> at  <strong>" . $speed_2 . " " . $su . '.</strong>
    <ul>
        <li>What is the total distance covered? <q1></li></li>
        <li>What is the average speed? <q2></li>
         <li>How long did this journey last? <q3></li>
        </ul></p>
        ';
    $answer = array($distance_1 + $distance_2, ($distance_1 + $distance_2) / ($time_1 + $time_2), $time_1 + $time_2);

    if ($L == 'advance') {

        $speed_1 = rand(10, 200);
        $time_1 = rand(1, 2 * 4) / 4;
        $speed_2 = rand(0, min($speed_1, 40));

        $distance_1 = $speed_1 * $time_1;
        $time_2 = $distance_1 / ($speed_1 - $speed_2);

        $ctimes = ["9:00", "10:30", "11:00", "12:00"];
        $ctime = $ctimes[rand(0, count($ctimes) - 1)];

        $stimes = [10, 20, 30, 40, 50, 60];
        $stime = $stimes[rand(0, count($stimes) - 1)];

        $question = "<p class='lead'>A train leaves Town A at " . $ctime . " to go to Town B.
        <ul>
        <li> It travels at an average speed of " . $speed_1 . " " . $su . " and reaches Town B after " . hoursandmins($time_1*60) . ".
        Calculate the distance covered by the train in $su <q1></li></li>
        <li>After spending $stime mins in Town B, the train goes back to Town A by the same route. It's speed is now $speed_2 $su less.
        What time does it reach Town A? <q2></li></li>
        </ul></p>";
        $endTime = strtotime($ctime) + ($time_1+$time_2)*60*60+$stime*60;
        $endTime  = date('H:i', $endTime);
        $answer = array($distance_1, $endTime, 0);
    }
    array_push($questions, $question);

    /* $question = '<p class="lead">Find the time taken by ' . $vehivle['prefix'] . " " . $vehivle['name'] . " " . $vehivle['ing'] . " with an average speed of <strong>" . $speed . " " . $su . "</strong> to complete a journey of <strong>" . $distance . " " . $du . "</strong>?</p>";
        array_push($questions, $question);

        $question = '<p class="lead">' . $vehivle['prefix'] . " " . $vehivle['name'] . " " . $vehivle['verb'] . "<strong> " . $distance . " " . $du . "</strong> at a speed of <strong>" . $speed . " " . $su . ".</strong> How much time will it take to complete this journey??</p>";
        array_push($questions, $question);
 */
    //$question = $questions[rand(0, count($question) + 1)];


    return array(
        'q' => $question,
        'a' => $answer,
    );
}


function hoursandmins($time, $format = '%02d hours and %02d minutes')
{
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    if($hours==0){
        return sprintf('%02d minutes', $minutes);
    }else if($minutes==0){
        return sprintf('%02d hours', $hours);
    }
    return sprintf($format, $hours, $minutes);
}


function check($ans, $cans)
{
    if (round($ans, 2) == round($cans, 2)) {
        return true;
    } else {
        return false;
    }
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
    if(is_array($data)){
        foreach ($data as $record) {
            $question = str_replace("'","\'", $record['q']);
            $answer = $record['a'];

            $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '100', 'speed-time-distance')";
            if (!mysqli_query($conn, $query)) {
                return false;
            }
        }
    }

    return true;
}