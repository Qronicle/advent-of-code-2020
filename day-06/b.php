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
            $uniqueAnswers[$answer] = isset($uniqueAnswers[$answer]) ? $uniqueAnswers[$answer]+1 : 1;
        }
    }
    $numPeople = count($people);
    $numAll = 0;
    foreach ($uniqueAnswers as $answer => $numAnswers) {
        if ($numAnswers == $numPeople) {
            $numAll++;
        }
    }
    $total += $numAll;
}

echo "$total\n";