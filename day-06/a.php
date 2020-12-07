<?php

require __DIR__ . '/../common/common.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$groups = explode("\n\n", $input);

$total = 0;
foreach ($groups as $group) {
    $people = explode("\n", $group);
    $uniqueAnswers = [];
    foreach ($people as $person) {
        $answers = str_split($person);
        foreach ($answers as $answer) {
            $uniqueAnswers[$answer] = true;
        }
    }
    $total += count($uniqueAnswers);
}

echo "$total\n";