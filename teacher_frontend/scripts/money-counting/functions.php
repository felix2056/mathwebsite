<?php

function generate($level)
{
    $question = "";
    $myfile = fopen("db/db.json", "r") or die("Unable to open file!");
    $db = json_decode(fread($myfile, filesize("db/db.json")), true);
    $coins = $db["money"]["coins"];
    $notes = $db["money"]["notes"];
    if ($level == "easy") {
        $question = $question . '
        <p>Count the following coins</p>
        <ul class="counting-money">';

        $cost = 0;
        $NoOfCoins = 0;
        while (true) {
            $coin = $coins[rand(0, count($coins) - 1)];
            if( $coin["value"]>1){
                continue;
            }
            $NoOfCoins = $NoOfCoins+1;
            if ($cost + $coin["value"] >= rand(1,12) & $cost != 0 | $NoOfCoins>7) {
                break;
            }
            $cost = $cost + $coin["value"];
            $question = $question . '<li><img class="coin img-fluid" src=' . $coin["location"] . '></li>';
        }
        $question = $question . "</ul>Rs. <q1>";
        $answer = [round($cost, 2)];
    } else if ($level == "normal") {
        $question = $question . '
        <p>Count the following coins</p>
        <ul class="counting-money">';

        $cost = 0;
        for ($i = 0; $i < rand(1, 3); $i++) {
            $note = $notes[rand(0, count($notes) - 1)];
            $cost = $cost + $note["value"];
            $question = $question . '<li><img class="note img-fluid" src=' . $note["location"] . '></li>';
        }
        for ($i = 0; $i < rand(1, 3); $i++) {
            $coin = $coins[rand(0, count($coins) - 1)];
            $cost = $cost + $coin["value"];
            $question = $question . '<li><img class="coin img-fluid" src=' . $coin["location"] . '></li>';
        }
        $question = $question . "</ul>Rs. <q1>";
        $answer = [round($cost, 2)];
    } else {
        while (true) {
            $costs = [];
            $real_cost = rand(100, 500);
            $question = $question . '<p class="lead">A chocolate cake costs <strong>Rs ' . $real_cost . '</strong>
        </p>
        <p>Which bank note(s) is the most appropriate to use?</p>
        <div class="money-choice form-group">
        ';

            for ($i = 0; $i < 3; $i++) {

                $cost = 0;
                $question = $question . '<div class="custom-control custom-radio">
            <input type="radio" class="custom-control-input" name="moneyChoice" value="' . ($i + 1) . '" id="opt' . ($i + 1) . '">
            <label class="custom-control-label" for="opt' . ($i + 1) . '">';
            $NoOfNotes= 0;
                while (true) {
                    $note = $notes[rand(0, count($notes) - 1)];
                    $cost = $cost + $note["value"];
                    $question = $question . '<img src="' . $note["location"] . '" class="img-fluid" alt="100" />';
                    $NoOfNotes = $NoOfNotes+1;
                    if ($cost >= $real_cost or $NoOfNotes>=3) {
                        array_push($costs, $cost);
                        break;
                    }
                }
                $question = $question . '</label></div>';
            }
            $opt_index = find_optimal_choice($real_cost, $costs);
            $question = $question . "<op-ch></div>";
            $answer = [$opt_index];

            if ($opt_index > 0 and count($costs) == count(array_unique($costs))) {
                break;
            } else {
                $question = "";
            }
        }
    }
    return array(
        'q' => $question,
        'a' => $answer
    );
}

function find_optimal_choice($real_cost, $costs)
{
    $MIN = 1000000;
    foreach ($costs as $i => $cost) {
        if ($MIN > $cost and $cost - $real_cost > 0) {
            $MIN = $cost;
            $opt_index = $i + 1;
        }
    }

    return $opt_index;
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
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '123', '656', 'money-counting')";
            if (!mysqli_query($conn, $query)) {
                return false;
            }
        }
    }

    return true;
}