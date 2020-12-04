<?php

require __DIR__ . '/../common/common.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = explode("\n", $input);

$numValid = 0;
foreach ($lines as $line) {
    preg_match('/^(\d+)\-(\d+)\s(.):\s(.*)$/', $line, $matches);
    $min = $matches[1];
    $max = $matches[2];
    $char = $matches[3];
    $password = $matches[4];
    $count = substr_count($password, $char);
    if ($count >= $min && $count <= $max) {
        $numValid++;
    }
}

echo "$numValid\n";