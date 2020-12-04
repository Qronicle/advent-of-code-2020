<?php

require __DIR__ . '/../common/common.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$numbers = explode("\n", $input);
$count = count($numbers);

for ($i = 0; $i < $count - 1; $i++) {
    for ($j = $i+1; $j < $count; $j++) {
        if ($numbers[$i] + $numbers[$j] == 2020) {
            echo $numbers[$i] * $numbers[$j];
            exit;
        }
    }
}
