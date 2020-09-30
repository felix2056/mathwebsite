<?php

function generate($level, $operation)
{
   global $solution;

   if ($operation == "plus") {
      if ($level == "easy") {
         $S = 0;
         $M = rand(0, 60);
         $H = 0;

         $s1 = 0;
         $m1 = rand(0, $M);
         $h1 = rand(0, 10);

         $s2 = 0;
         $m2 = $M - $m1;
         $h2 = rand(0, 10);

         $S = $S;
         $M = $M;
         $H = $h1 + $h2;
      } else if ($level == "normal") {
         $S = 0;
         $M = 0;
         $H = 0;

         $s1 = 0;
         $m1 = rand(0, 60);
         $h1 = rand(0, 10);

         $s2 = 0;
         $m2 = rand(0, 60);
         $h2 = rand(0, 10);

         $S = $S;
         $M = $m1 + $m2 - 60 * (int)(($m1 + $m2) / 60);
         $H = $h1 + $h2 + (int)(($m1 + $m2) / 60);
      } else {
         $S = 0;
         $M = 0;
         $H = 0;

         $s1 = rand(0, 60);
         $m1 = rand(0, 60);
         $h1 = rand(0, 10);

         $s2 = rand(0, 60);
         $m2 = rand(0, 60);
         $h2 = rand(0, 10);

         $S = ($s1 + $s2) % 60;
         $M = (($m1 + $m2 + (int)(($s1 + $s2) / 60))) % 60;
         $H = $h1 + $h2 + (int)(($m1 + $m2) / 60) +  +(int)(($s1 + $s2) / 3600);
      }
      $question = '<div>
    <span>&' . $operation . ';</span>
    <table class="table table-bordered">
       <thead class="thead-light">
          <tr>
             <th scope="col">H</th>
             <th scope="col">M</th>
             <th scope="col">S</th>
          </tr>
       </thead>
       <tbody>
          <tr>
             <td>' . $h1 . '</td>
             <td>' . $m1 . '</td>
             <td>' . $s1 . '</td>
          </tr>
          <tr>
            <td>' . $h2 . '</td>
            <td>' . $m2 . '</td>
            <td>' . $s2 . '</td>
          </tr>
          <tr>
             <td><q1></td>
             <td><q2></td>
             <td><q3></td>
          </tr>
       </tbody>
    </table>
 </div>
 <chbtn>
 <div class="solution" id="<sol>" style="display:none">
 <p><strong>Solution</strong></p>
 <table>
    <tbody>
        <tr>
            <td>' . $s1 . ' seconds + ' . $s2 . ' seconds =</td>
            <td>00 hours</td>
            <td>' . ((int)(($s1 + $s2) / 60)) . ' mins</td>
            <td>' . (($s1 + $s2) % 60) . ' seconds</td>
        </tr>
       <tr>
          <td>' . $m1 . ' mins + ' . $m2 . ' mins =</td>
          <td>' . ((int)(($m1 + $m2) / 60)) . ' hour</td>
          <td>' . (($m1 + $m2) % 60) . ' mins</td>
          <td>00 seconds</td>
       </tr>
       <tr>
          <td>' . $h1 . ' hours + ' . $h2 . ' hours = </td>
          <td>' . ($h1 + $h2) . ' hours</td>
          <td>00 mins</td>
          <td>00 seconds</td>
       </tr>
       <tr>
          <td>Answer = </td>
          <td>' . $H . ' hours</td>
          <td>' . $M . ' mins</td>
          <td>' . $S . ' seconds</td>
       </tr>
    </tbody>
 </table>
</div>
 ';

 $solutionHTML = '<div class="solution" id="<sol>">
 <table>
    <tbody>
        <tr>
            <td>' . $s1 . ' seconds + ' . $s2 . ' seconds =</td>
            <td>00 hours</td>
            <td>' . ((int)(($s1 + $s2) / 60)) . ' mins</td>
            <td>' . (($s1 + $s2) % 60) . ' seconds</td>
        </tr>
       <tr>
          <td>' . $m1 . ' mins + ' . $m2 . ' mins =</td>
          <td>' . ((int)(($m1 + $m2) / 60)) . ' hour</td>
          <td>' . (($m1 + $m2) % 60) . ' mins</td>
          <td>00 seconds</td>
       </tr>
       <tr>
          <td>' . $h1 . ' hours + ' . $h2 . ' hours = </td>
          <td>' . ($h1 + $h2) . ' hours</td>
          <td>00 mins</td>
          <td>00 seconds</td>
       </tr>
       <tr>
          <td>Answer = </td>
          <td>' . $H . ' hours</td>
          <td>' . $M . ' mins</td>
          <td>' . $S . ' seconds</td>
       </tr>
    </tbody>
 </table>
</div>
 ';

 setSolution($solutionHTML);

   } else {
      if ($level == "easy") {
         $S = 0;
         $M = 0;
         $H = 0;

         $s1 = 0;
         $m1 = rand(0, 60);
         $h1 = rand(0, 10);

         $s2 = 0;
         $m2 = rand(0, $m1);
         $h2 = rand(0, $h1);

         $S = $s1 - $s2;
         $M = $m1 - $m2;
         $H = $h1 - $h2;
      } else if ($level == "normal") {

         $N1 = rand(0, 11 * 60);
         $N2 = rand(0, $N1);

         $s1 = 0;
         $m1 = $N1 % 60;
         $h1 = (int)($N1 / 60);

         $s2 = 0;
         $m2 = $N2 % 60;
         $h2 = (int)($N2 / 60);

         $N = $N1 - $N2;
         $S = 0;
         $M = $N % 60;
         $H = (int)($N / 60);
      } else {
         $N1 = rand(0, 11 * 3600);
         $N2 = rand(0, $N1);

         $s1 = ($N1 % 60);
         $m1 = (int)($N1 / 60) % 60;
         $h1 = (int)($N1 / 3600);

         $s2 = ($N2 % 60);
         $m2 = (int)($N2 / 60) % 60;
         $h2 = (int)($N2 / 3600);

         $N = $N1 - $N2;
         $S = ($N % 60);
         $M = (int)($N / 60) % 60;
         $H = (int)($N / 3600);
      }
      $question = '<div>
    <span>&' . $operation . ';</span>
    <table class="table table-bordered">
       <thead class="thead-light">
          <tr>
             <th scope="col">H</th>
             <th scope="col">M</th>
             <th scope="col">S</th>
          </tr>
       </thead>
       <tbody>
          <tr>
             <td>' . $h1 . '</td>
             <td>' . $m1 . '</td>
             <td>' . $s1 . '</td>
          </tr>
          <tr>
            <td>' . $h2 . '</td>
            <td>' . $m2 . '</td>
            <td>' . $s2 . '</td>
          </tr>
          <tr>
             <td><q1></td>
             <td><q2></td>
             <td><q3></td>
          </tr>
       </tbody>
    </table>
 </div>
 <chbtn>';
      if ($s1 < $s2) {
         $s1 = $s1 + 60;
         $m1 = $m1 - 1;
      }
      if ($m1 < $m2) {
         $m1 = $m1 + 60;
         $h1 = $h1 - 1;
      }
      $question .= '
 <div class="solution" id="<sol>" style="display:none">
 <p><strong>Solution</strong></p>
 <table>
    <tbody>
        <tr>
            <td>' . $s1 . ' seconds - ' . $s2 . ' seconds =</td>
            <td>00 hours</td>
            <td>00 mins</td>
            <td>' . ($s1 - $s2) . ' seconds</td>
        </tr>
       <tr>
          <td>' . $m1 . ' mins - ' . $m2 . ' mins =</td>
          <td>00 hour</td>
          <td>' . ($m1 - $m2) . ' mins</td>
          <td>00 seconds</td>
       </tr>
       <tr>
          <td>' . $h1 . ' hours - ' . $h2 . ' hours = </td>
          <td>' . ($h1 - $h2) . ' hours</td>
          <td>00 mins</td>
          <td>00 seconds</td>
       </tr>
       <tr>
          <td>Answer = </td>
          <td>' . $H . ' hours</td>
          <td>' . $M . ' mins</td>
          <td>' . $S . ' seconds</td>
       </tr>
    </tbody>
 </table>
</div>
 ';

 $solutionHTML = '
 <div class="solution" id="<sol>">
 <table>
    <tbody>
        <tr>
            <td>' . $s1 . ' seconds - ' . $s2 . ' seconds =</td>
            <td>00 hours</td>
            <td>00 mins</td>
            <td>' . ($s1 - $s2) . ' seconds</td>
        </tr>
       <tr>
          <td>' . $m1 . ' mins - ' . $m2 . ' mins =</td>
          <td>00 hour</td>
          <td>' . ($m1 - $m2) . ' mins</td>
          <td>00 seconds</td>
       </tr>
       <tr>
          <td>' . $h1 . ' hours - ' . $h2 . ' hours = </td>
          <td>' . ($h1 - $h2) . ' hours</td>
          <td>00 mins</td>
          <td>00 seconds</td>
       </tr>
       <tr>
          <td>Answer = </td>
          <td>' . $H . ' hours</td>
          <td>' . $M . ' mins</td>
          <td>' . $S . ' seconds</td>
       </tr>
    </tbody>
 </table>
</div>
 ';

 setSolution($solutionHTML);
   }

   $answer = [$H, $M, $S];
   return array(
      'q' => $question,
      'a' => $answer
   );
}

function setSolution($solutionHTML) {
   $solutionFILE = fopen('solution.txt', 'w') or die("Unable to open file!");

   fwrite($solutionFILE, $solutionHTML);
   fclose($solutionFILE);
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

   $solutionFILE = fopen("solution.txt", "r") or die("Unable to open file!");
   $solution = fread($solutionFILE, filesize("solution.txt"));
   
   fclose($solutionFILE);

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

      $query = "INSERT INTO maths_quiz_questions (id_Maths_Excercise_Sets_FK, Question, Answer, Solution, Question_Weight, Question_Topic) 
            VALUES('$info[maths_quiz_excercise_sets_last_id]', '$question', '$answer', '$solution', '100', 'time')";

      if (!mysqli_query($conn, $query)) {
         return false;
      }
   }

   return true;
}
