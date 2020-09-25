<?php

function generate($L)
{
    $myfile = fopen("db/db.json", "r") or die("Unable to open file!");
    $db = json_decode(fread($myfile, filesize("db/db.json")), true);
    fclose($myfile);


    $person = $db['names'][rand(0, count($db['names']) - 1)];
    $name = $person["name"];
    $heShe = ($person["gender"] == "Male") ? "he" : "she";
    $hisher = ($person["gender"] == "Male") ? "his" : "her";

    $currencies = [
        array(
            "symbol" => '£',
            "rate" => rand(40, 50)
        ), array(
            "symbol" => '$',
            "rate" => rand(30, 35)
        ), array(
            "symbol" => '€',
            "rate" => rand(40, 45)
        ),
    ];

    $currency = $currencies[rand(0, 2)];
    $rate = $currency["rate"];
    $symbol = $currency["symbol"];


    if ($L == 'easy') {
        $amount = rand(1, 500);
        $question = "<p><strong>Usually ".$symbol."1 = Rs $rate</strong></p>
        <p>$name changes <strong>$symbol $amount</strong> at the bank.</p> <p>How much money does $heShe have in  <strong> rupees?</strong><q1></p>";
        $answer = [$rate * $amount, 0];
    } elseif ($L == 'normal') {
        $amount = rand(2, 1000);
        if (rand(0, 1)) {
            $spent = ceil(rand($amount / 2, $amount - 1) * $rate/100)*100;
            $question = "<p><strong>Usually ".$symbol."1 = Rs $rate</strong></p>
            <p>$name changes <strong>$symbol $amount</strong> $heShe goes to the local bazar and spends Rs $spent on goods.</p>
            <p>How much money does $heShe have left in  <strong> rupees?</strong><q1></p>";
            $answer = [$rate * $amount - $spent, 0];
        } else {
            $question = "<p><strong>Usually ".$symbol."1 = Rs $rate</strong></p>
            <p>$name needs  <strong>$symbol $amount</strong> for $hisher trip.</p> <p>How many <strong>rupees</strong> must $heShe
             give to the bank to get this amount?<q1></p>";
            $answer = [$rate * $amount, 0];
        }
    }elseif ($L == 'advance') {
        $amount = rand(1, 500);
        $price = rand($amount, $amount*2) * $rate;
        $question = "<p><strong>Usually ".$symbol."1 = Rs $rate</strong></p>
        <p>$name buys a watch for  <strong>$symbol $amount</strong> and sells it for Rs $price.
        </p><p>Calculate the <strong> profit</strong> $heShe makes in <strong> rupees?</strong><q1></p>";
        $answer = [$price- $rate * $amount, 0];
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
    if(is_array($data)){
        foreach ($data as $record) {
            $question = $record['q'];
            $answer = $record['a'][0];

            $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '100', 'money-conversion')";
            if (!mysqli_query($conn, $query)) {
                return false;
            }
        }
    }

    return true;
}
