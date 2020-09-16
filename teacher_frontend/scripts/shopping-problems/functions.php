<?php

function generate($L)
{
    $myfile = fopen("db/db.json", "r") or die("Unable to open file!");
    $db = json_decode(fread($myfile, filesize("db/db.json")), true);
    fclose($myfile);

    $FOODS = $db['foods'];
    $indexes = array_rand($FOODS, 10);
    $foods = [];
    foreach ($indexes as $i) {
        array_push($foods, $FOODS[$i]);
    }

    $person = $db['names'][rand(0, count($db['names']) - 1)];
    $name = $person["name"];
    $heShe = ($person["gender"]=="Male")?"he":"she";

    if ($L == 'easy' || $L == 'normal') {
        $question = '<table class="shopping-menu table table-bordered table-striped table-sm">
        <tbody>';
        for ($i = 0; $i < count($foods); $i++) {
            if ($L == 'easy') {
                $foods[$i]['price'] = rand($foods[$i]['price_min'], $foods[$i]['price_max']);
            } else {
                $foods[$i]['price'] = rand($foods[$i]['price_min'], $foods[$i]['price_max'] * 4) / 4;
            }
        }
        for ($i = 0; $i < count($foods) / 2; $i++) {
            $question = $question . '<tr>
            <td><span>' . $foods[2 * $i]['food'] . '</span><span class="badge">Rs ' . number_format($foods[2 * $i]['price'],2) . '</span></td>
            <td><span>' . $foods[2 * $i + 1]['food'] . '</span><span class="badge">Rs ' . number_format($foods[2 * $i + 1]['price'],2) . '</span></td>
         </tr>';
        }
        $indexes = array_rand($foods, 10);
        $items = [];
        foreach ($indexes as $i) {
            array_push($items, $foods[$i]);
        }
        shuffle($items);
        $item1 = $items[0]['food'];
        $item2 = $items[1]['food'];
        $item3 = $items[2]['food'];
        $item4 = $items[3]['food'];
        $item5 = $items[4]['food'];
        $item6 = $items[5]['food'];
        $item7 = $items[6]['food'];
        $item8 = $items[7]['food'];
        $item9 = $items[8]['food'];

        $moneyHas1 =  ceil(rand($items[5]['price'], $items[5]['price']*2)/100)*100;
        $moneyHas2 =  ceil(rand($items[6]['price'] + $items[7]['price'], ($items[6]['price'] + $items[7]['price'])*2)/100)*100;


        $qty =  rand(1, 10);
        $moneyHas3 =  ceil(rand($items[8]['price'] * $qty, $items[8]['price'] * $qty*2)/100)*100;

        $questions = [
            "<p>What is the total cost of an $item1 + $item2?</p> <strong>Rs</strong>",
            "How much money does $name need to buy a $item3+$item4+$item5? <strong>Rs</strong>",
            "If $name buys a $item6 with Rs $moneyHas1. How much money will $name have left? <strong>Rs</strong>",
            "$name buys $item7 and $item8. $name pays for these items with Rs $moneyHas2. How much change will be received?",
            "$name buys $qty of $item9. How much change will be received from Rs $moneyHas3?"
        ];
        $answers = [
            $items[0]['price'] + $items[1]['price'],
            $items[2]['price'] + $items[3]['price'] + $items[4]['price'],
            $moneyHas1 - $items[5]['price'],
            $moneyHas2 - ($items[6]['price'] + $items[7]['price']),
            $moneyHas3 - $items[8]['price'] * $qty

        ];
        $indexes = array_rand($questions, 3);
        $question = $question . "</tbody></table><ul>";
        $answer = [];
        foreach ($indexes as $j => $i) {
            $question = $question . "<li>" . $questions[$i] . " <q" . ($j + 1) . "></li>";
            array_push($answer, $answers[$i]);
        }


        $question = $question . "</ul>";
    } elseif ($L == 'advance') {
        $question = ' <p>' . $name . ' went to the store and purchased the following items.</p>
        <p>When ' . $name . ' went home '.$heShe.' saw the total was missing from the receipt.</p>
        <p>Study the receipt carefully and answer the following questions</p>
        <table class="shopping-receipt table table-bordered table-striped table-sm">
           <thead>
              <tr>
                 <th>Qty</th>
                 <th>Item</th>
                 <th>Price</th>
              </tr>
           </thead>
           <tbody>';
        for ($i = 0; $i < count($foods); $i++) {
            $foods[$i]['price'] = rand($foods[$i]['price_min'], $foods[$i]['price_max']);
            $foods[$i]['qty'] = rand(1, 10);
        }
        for ($i = 0; $i < 3; $i++) {
            $question =  $question . '<tr>
            <td>' . $foods[$i]['qty'] . '</td>
            <td>' . $foods[$i]['food'] . '</td>
            <td>Rs ' . number_format($foods[$i]['price']*$foods[$i]['qty'],2) . '</td>
         </tr>';
        }

        $question =  $question . '<tr>
                 <td></td>
                 <td>Total</td>
                 <td></td>
              </tr>
           </tbody>';

    
        $item1 = $foods[0]['food'];
        $item2 = $foods[1]['food'];
        $item3 = $foods[2]['food'];
        $cost = $foods[0]['price'] * $foods[0]['qty'] + $foods[1]['price'] * $foods[1]['qty'] + $foods[2]['price'] * $foods[2]['qty'];
        $moneyHas =  ceil(rand($cost,$cost*2)/100)*100;

        $question = $question . "</tbody></table><ul>";

        $itmIndx = rand(0,2);
        $question = $question . "<li>What is the price of one ".$foods[ $itmIndx ]['food']."<q1></li>";
        $question = $question . "<li>What is the total cost of all the items purchased by $name<q2></li>";
        $question = $question . "<li>$name paid for these items with Rs $moneyHas. How much change was given <q3></li>";

        $answer = [
            $foods[ $itmIndx ]['price'],
            $cost,
            $moneyHas - $cost
        ];


        $presentage = rand(1, 4) * 5;


        if (rand(0, 1)) {
            $cost = $cost + $cost * $presentage / 100;
            $moneyHas =  ceil(rand($cost, 5000)/100)*100;
            $question = $question . "<li>: $name went back to the shop the following week to buy the same items but all the prices increased by $presentage% 
            <ul><li>How much will $name have to pay now? <q4></li>
            <li>How much change will he received from Rs $moneyHas <q5></li>
            </ul>
            </li>";
            array_push($answer, $cost);
            array_push($answer, $moneyHas - $cost);
        } else {
            $itemIndx = rand(0, 2);
            $cost = $cost + $foods[$itemIndx]["price"] * $foods[$itemIndx]["qty"] * $presentage / 100;
            $moneyHas =  ceil(rand($cost, $cost*2)/100)*100;
            $question = $question . "<li>: $name went back to the shop the following week to buy the same items but the prices of " . $foods[$itemIndx]["food"] . " increased by $presentage% 
            <ul><li>How much will $name have to pay now? <q4></li>
            <li>How much change will he received from Rs $moneyHas . How much change will be given<q5></li>
            </ul>
            </li>";
            array_push($answer, $cost);
            array_push($answer, $moneyHas - $cost);
        }

        $question = $question . "</ul>";
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
