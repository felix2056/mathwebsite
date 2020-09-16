<?php
function fetch($opr, $f1, $f2)
{
    $n1 = rand(pow(10, $f1 - 1), pow(10, $f1) - 1);
    $n2 = rand(pow(10, $f2 - 1), pow(10, $f2) - 1);
    if ($opr == 'plus') {
        $q = "<p><span>&plus;</span><span>$n1</span><br /><span>$n2</span></p>";
        $a = $n1 + $n2;
    } else if ($opr == 'minus') {
        $n1 = rand(pow(10, $f1 - 1), pow(10, $f1) - 1);
        $n2 = rand(pow(10, $f2 - 1), min($n1, pow(10, $f2) - 1));
        $q = "<p><span>&minus;</span><span>$n1</span><br /><span>$n2</span></p>";
        $a = $n1 - $n2;
    } else if ($opr == 'multiply') {
        $q = "<p><span>&times;</span><span>$n1</span><br /><span>$n2</span></p>";
        $a = $n1 * $n2;
    } else if ($opr == 'divide') {
        $n1 = rand(pow(10, $f1 - 1), pow(10, $f1) - 1);
        $n2 = rand(pow(10, $f2 - 1), min($n1, pow(10, $f2) - 1));

        $a = $n1 / $n2;
        $a = (int) $a;
        $n1 = $a * $n2;
        $q = "<p><span>&divide;</span><span>$n1</span><br /><span>$n2</span></p>";
        $a = $n1 / $n2;
    }
    return array(
        "ans" => round($a, 2),
        "q" => $q
    );
}
?>