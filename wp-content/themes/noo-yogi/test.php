<?php
echo strlen('Juan Pablo II 365, Villa Panorama');
die();

$t1 = strtotime('15:30');
$t2 = strtotime('16:00');

// echo '<br/>';

// echo '<br/>';

$t3 = $t2 - $t1;
// echo $t3 - 1;
echo $t3;
echo '<br/>';
echo date('H', $t3);
