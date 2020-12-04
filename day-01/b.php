<?php

require __DIR__ . '/../common/common.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$numbers = explode("\n", $input);
$count = count($numbers);

for ($i = 0; $i < $count - 2; $i++) {
    for ($j = $i+1; $j < $count - 1; $j++) {
        for ($k = $i+2; $k < $count; $k++)
        if ($numbers[$i] + $numbers[$j] + $numbers[$k] == 2020) {
            echo $numbers[$i] * $numbers[$j] * $numbers[$k];
            exit;
        }
    }
}