<?php

function generate($L)
{
    $myfile = fopen("db/db.json", "r") or die("Unable to open file!");
    $db = json_decode(fread($myfile, filesize("db/db.json")), true);
    fclose($myfile);

    $peopleIdx = array_rand($db['names'], 10);
    $people = [];
    foreach ($peopleIdx as $i) {
        array_push($people, $db['names'][$i]);
    }
    shuffle($people);

    $person1 =  $people[0];
    $name1 = $person1["name"];
    $heShe1 = ($person1["gender"] == "Male") ? "he" : "she";
    $hisher1 = ($person1["gender"] == "Male") ? "his" : "her";

    $person2 =  $people[1];
    $name2 = $person2["name"];
    $heShe2 = ($person2["gender"] == "Male") ? "he" : "she";
    $hisher2 = ($person2["gender"] == "Male") ? "his" : "her";

    $person3 =  $people[2];
    $name3 = $person3["name"];
    $heShe3 = ($person3["gender"] == "Male") ? "he" : "she";
    $hisher3 = ($person3["gender"] == "Male") ? "his" : "her";




    $questions = [];
    if ($L == 'easy') {
        $weight = rand(1, 40);
        $moreWeight =  rand(1, $weight);
        $question = "<p>$name1 ’s shopping weighs $weight kg and weighs <strong>$moreWeight kg more</strong> than $name2.</p>
        <p>What is the weight of $name2 ’s shopping?<q1></p>";
        array_push($questions, $question);
        $times = rand(2, 5);
        $nuOfSweets = rand(1, 10) * ($times + 1);
        $food = $db['food'][rand(0, count($db['food']) - 1)];
        $question = "<p>$name1 and $name2 eat $nuOfSweets of $food althogether. $name2 eats <strong>$times</strong> times as many pieces of $food as $name1. </p>
        <p>How many pieces of $food does $name1 eat?<q2></p>";
        array_push($questions, $question);
        $question = $questions[rand(0, count($questions) - 1)];
        $answer = [$weight - $moreWeight, $nuOfSweets / ($times + 1)];
    } elseif ($L == 'normal') {
        $itemshas1 = rand(10, 50);
        $itemshas2 = rand(10, 50);

        $times = rand(2, 5);
        $item = $db['goods'][rand(0, count($db['goods']) - 1)];
        $question = "<p>$name1 has $itemshas1 $item. $name2 has </strong>$times</strong> times as many $item as $name1. $name3 has $itemshas2 $item more than $name2. </p>
        <p>How many $item does $name3 have?<q1></p>";
        array_push($questions, $question);

        $marksOfLower = rand(10, 90);
        $moreMarks = rand($marksOfLower, 100);

        $marks = $marksOfLower * 2 + $moreMarks;
        $question = "<p>$name1 and $name2 obtain a total of $marks marks in a test. $name1 obtains $moreMarks marks more than $name2. </p>
        <p>How many marks does $name2 obtain in the test?<q2></p>";
        array_push($questions, $question);


        $itemIndex = array_rand($db['goods'], 2);
        $items = [];
        foreach ($itemIndex as $i) {
            array_push($items, $db['goods'][$i]);
        }
        $item1 = $items[0];
        $item2 = $items[1];
        $cost2 = rand(5, 50);
        $times = rand(2, 5);
        $cost1 = $cost2 * $times;
        $qt1 = rand(1, 15);
        $qt2 = rand(1, 15);
        $cost = $qt1 * $cost1 + $qt2 * $cost2;
        $moneyHas = ceil(rand($cost, $cost * 2) / 10) * 10;

        $question = "<p>$name1 has Rs $moneyHas. $heShe1 uses Rs $cost to buy $qt1 $item1 and $qt2 $item2.
        Each $item1 costs $times times as much as a $item2.</p>
        <p>Find the cost of one $item1.<q3></p>";
        array_push($questions, $question);

        $question = $questions[rand(0, count($questions) - 1)];
        $answer = [$itemshas1 * $times + $itemshas2, ($marks - $moreMarks) / 2, $cost1];
    } elseif ($L == 'advance') {
        $itemIndex = array_rand($db['goods'], 2);
        $items = [];
        foreach ($itemIndex as $i) {
            array_push($items, $db['goods'][$i]);
        }
        $item1 = $items[0];
        $item2 = $items[1];
        $cost1 = rand(5, 50);
        $cost2 = rand(5, 50);
        $qt1 = rand(1, 15);
        $qt2 = rand(1, 15);
        $qt =  rand(1, ($qt1 * $cost1 + $qt2 * $cost2) / $cost2);
        $d = $qt1 * $cost1 + $qt2 * $cost2 - $qt * $cost2;
        $question = "<p>$name1 and $name2 have the same amount of pocket money</p>
        <p>Using $hisher1 pocket money, $name1 tries to buy $qt1 $item1 which cost Rs $cost1 each and $qt2 $item2. 
        However, $heShe1 needs Rs $d more to be able to do so.</p>
        <p>Using $hisher2 pocket money, $name2 buys $qt $item2. $heShe2 has no money left. </p>
        <p>How much money did $name1 and $name2 each have at the beginning?<q1></p>";
        array_push($questions, $question);

        $money = rand(20, 70);
        $r1 = rand(1, 6);
        $r2 = rand(1, 6);
        $money1 = $r1 * $money;
        $money2 = $r2 * $money;

        $given = rand(1, $money1);
        $money1 = $money1 - $given;
        $money2 = $money2 + $given;
        $question = "<p>$name1 has Rs $money1 and $name2 has Rs $money2.</p>
        <p>$name2 gives some of $hisher2 money to $name1.</p>
        <p>The ratio of the amount of money which $name1 and $name2 now have is $r1:$r2</p>
        <p>How much money did $name2 give to $name1?<q2></p>";
        array_push($questions, $question);

        $question = $questions[rand(0, count($questions) - 1)];
        $answer = [$qt * $cost2, $given];
    }

    $question = capitalize($question);
    return array(
        'q' => $question,
        'a' => $answer
    );
}

function capitalize($s){
    $S = explode('.',$s);

    foreach ($S as $key=> $s) {
        # code...
        $S[$key] = ucfirst(trim($s));
    }

    return implode('. ',$S);

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
        $question = $data[$i]['q'];
        $answer = json_encode($data[$i]['a']);
        
        $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Solution, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '123', '656', 'algebra-word-problems')";
        
        if (!mysqli_query($conn, $query)) {
            return false;
        }
    }

    return true;
}