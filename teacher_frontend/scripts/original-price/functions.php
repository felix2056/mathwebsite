<?php

function generate($L, $type)
{
	$myfile = fopen("db/db.json", "r") or die("Unable to open file!");
	$db = json_decode(fread($myfile, filesize("db/db.json")), true);
	fclose($myfile);

	$person1 = $db['names'][rand(0, count($db['names']) - 1)];
	$name1 = $person1["name"];
	$heShe1 = ($person1["gender"] == "Male") ? "he" : "she";
	$hisher1 = ($person1["gender"] == "Male") ? "his" : "her";

	$person2 = $db['names'][rand(0, count($db['names']) - 1)];
	$name2 = $person2["name"];
	$heShe2 = ($person2["gender"] == "Male") ? "he" : "she";
	$hisher2 = ($person2["gender"] == "Male") ? "his" : "her";

	$person3 = $db['names'][rand(0, count($db['names']) - 1)];
	$name3 = $person3["name"];
	$heShe3 = ($person3["gender"] == "Male") ? "he" : "she";
	$hisher3 = ($person3["gender"] == "Male") ? "his" : "her";

	$questions = [];
	$answer = [];
	$solution = [];
	if ($L == 'easy' || $L == 'normal') {
		if ($type == "increase") {
			$goods = array_merge($db['food'], $db['goods']);
			$good = $goods[rand(0, count($goods)) - 1];

			if ($L == "easy") {
				$presentage = rand(1, 5) * 5;
				$originalPrice = rand(1, 20) * 20;
			} else {
				$presentage = rand(1, 20);
				$originalPrice = rand(10, 500);
			}

			$newPrice = $originalPrice * (1 + $presentage / 100);
			$question = "<p>The price of $good in a supermarket has increased by $presentage%. The new price is Rs. " . number_format($newPrice, 2) . "</p>
        <p>Calculate the <strong>original price</strong> of the $good?</p>
        <p><span>Ans:</span><q1></p>";

			array_push($questions, $question);
			array_push($answer, round($originalPrice, 2));
			$sol = "<p>Solution
    Rs " . number_format($newPrice, 2) . " = 1" . $presentage . "% of original price –> (100% + $presentage% increase = 1$presentage%)
    Rs " . number_format($newPrice, 2) . "/ " . (($presentage + 100) / 100) . "
    Original price = Rs " . number_format($originalPrice, 2) . "</p>";
			array_push($solution, $sol);

			$question = "<p>It was announced that a $good going to cost $presentage% more than its <strong>original price</strong>. If it is sold at Rs " . number_format($newPrice, 2) . "</p>
        <p>what was the <strong>original price</strong>?</p>
        <p><span>Ans:</span><q2></p>";
			array_push($questions, $question);
			array_push($answer, round($originalPrice, 2));

			$sol = "<p>Solution
    Rs " . number_format($newPrice, 2) . " = 1" . $presentage . "% of original price –> (100% + $presentage% more = 1$presentage%)
    Rs " . number_format($newPrice, 2) . "/ " . (($presentage + 100) / 100) . "
    Original price = Rs " . number_format($originalPrice, 2) . "</p>";
			array_push($solution, $sol);
		} else {
			$goods = array_merge($db['food'], $db['goods']);
			$good = $goods[rand(0, count($goods)) - 1];

			if ($L == "easy") {
				$presentage = rand(1, 5) * 5;
				$originalPrice = rand(1, 20) * 20;
			} else {
				$presentage = rand(1, 20);
				$originalPrice = rand(10, 500);
			}

			$newPrice = $originalPrice * (1 - $presentage / 100);
			$question = "<p>In a sale, the <strong>original price</strong> of a $good is reduced by $presentage%. After the reduction, the $good costs  Rs. " . number_format($newPrice, 2) . "</p>
        <p>Calculate the <strong>original price</strong> of the $good?</p>
        <p><span>Ans:</span><q1></p>";
			array_push($questions, $question);
			array_push($answer, round($originalPrice, 2));

			$sol = "<p>Solution
    Rs " . number_format($newPrice, 2) . " = " . (100 - $presentage) . "% of original price –> (100% - $presentage% reduce = " . (100 - $presentage) . "%)
    Rs " . number_format($newPrice, 2) . "/ " . ((100 - $presentage) / 100) . "
    Original price = Rs " . number_format($originalPrice, 2) . "</p>";
			array_push($solution, $sol);

			$question = "<p>The price of a $good after a discount of $presentage% is $" . number_format($newPrice, 2) . "</p>
        <p></p>What is the <strong>original price</strong> of the $good?</p>
        <p><span>Ans:</span><q2></p>";
			array_push($questions, $question);
			array_push($answer, round($originalPrice, 2));
			$sol = "<pre>Solution
    Rs " . number_format($newPrice, 2) . " = " . (100 - $presentage) . "% of original price –> (100% - $presentage% discount = " . (100 - $presentage) . "%)
    Rs " . number_format($newPrice, 2) . "/ " . ((100 - $presentage) / 100) . "
    Original price = Rs " . number_format($originalPrice, 2) . "<pre>";
			array_push($solution, $sol);
		}
		$question = $questions[rand(0, count($questions) - 1)];
	} else {
		$goods = $db['goods_b'];
		$good = $goods[rand(0, count($goods)) - 1];

		$presentage = rand(1, 25);
		$costPerOne = rand(10, 500);
		$NoOfFriends = rand(2, 6);

		$originalPrice = $costPerOne * $NoOfFriends * (100 / (100 - $presentage));
		$question = "<p>$NoOfFriends friends buy a gift for their teacher. After receiving a discount of $presentage% they each pay Rs " . number_format($costPerOne, 2) . "</p>
        <p></p>What was the <strong>original price</strong> of the gift?</p>
        <p><span>Ans:</span></p><q1>";
		array_push($questions, $question);
		array_push($answer, round($originalPrice, 2));

		$sol = "<p>Solution
    Cost  = $costPerOne * $NoOfFriends = Rs " . number_format($costPerOne * $NoOfFriends, 2) . "
    Rs " . number_format($costPerOne * $NoOfFriends, 2) . " = " . (100 - $presentage) . "% of original price –> (100% - $presentage% discount = " . (100 - $presentage) . "%)
    Rs " . number_format($costPerOne * $NoOfFriends, 2) . "/ " . ((100 - $presentage) / 100) . "
    Original price = Rs " . number_format($originalPrice, 2) . "</p>";
		array_push($solution, $sol);

		$presentage = rand(1, 15);
		$noOFYears = rand(1, 4);
		$originalPrice = rand(10, 200) * 100;
		$costOfGood = $originalPrice * (1 - $presentage * $noOFYears / 100);

		$question = "<p>The price of a $good reduces by $presentage% each year.
        After $noOFYears years the $good is sold for Rs." . number_format($costOfGood, 2) . "</p>
        <p></p>What was the <strong>original price</strong> of the $good?</p>
        <p><span>Ans:</span></p><q2>";
		array_push($questions, $question);
		array_push($answer, round($originalPrice, 2));

		$sol = "<p>Solution
    Rs " . number_format($costOfGood, 2) . " = " . (100 - $presentage * $noOFYears) . "% of original price –> (100% - " . ($presentage * $noOFYears) . "% ( $presentage &times; $noOFYears) reduce = " . (100 - $presentage * $noOFYears) . "%)
    Rs " . number_format($costOfGood, 2) . "/ " . ((100 - $presentage * $noOFYears) / 100) . "
    Original price = Rs " . number_format($originalPrice, 2) . "</p>";
		array_push($solution, $sol);

		$question = $questions[rand(0, count($questions) - 1)];
	}

	return array(
		'q' => $question,
		'a' => $answer,
		's' => $solution
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
			$solution = $record['s'][0];

            $query ="INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Solution, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '$solution', '100', 'original-price')";
            if (!mysqli_query($conn, $query)) {
                return false;
            }
        }
    }

    return true;
}