<?php
echo '<h1>Operaciones aritmeticas</h1>';

$a = $_GET['n1'];
$b = $_GET['n2'];

$c = $a + $b;
//echo "la suma de <u>$a</u> y $b es igual <b>$c</b>";
//print("la suma de <u>$a</u> y $b es igual <b>$c</b>");
printf("la suma de <u>%d</u> y %d es igual <b>%d</b>",$a,$b,$c);