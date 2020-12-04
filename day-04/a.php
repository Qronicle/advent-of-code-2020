<?php

require __DIR__ . '/../common/common.php';

$input = file_get_contents(__DIR__ . '/input.txt');

$passports = explode("\n\n", $input);

$requiredKeys = ['byr', 'iyr', 'eyr', 'hgt', 'hcl', 'ecl', 'pid'];

$numValid = 0;
foreach ($passports as $passport) {
    // Parse passport data
    $content = explode(' ', str_replace("\n", ' ', $passport));
    $data = [];
    foreach ($content as $dataString) {
        $fields = explode(':', $dataString);
        $data[$fields[0]] = $fields[1];
    }
    // Validate
    $valid = true;
    foreach ($requiredKeys as $requiredKey) {
        if (!array_key_exists($requiredKey, $data)) {
            $valid = false;
            break;
        }
    }
    if ($valid) {
        $numValid++;
    }
}

echo "$numValid\n";