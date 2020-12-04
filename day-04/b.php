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
    // Check required fields
    $valid = true;
    foreach ($requiredKeys as $requiredKey) {
        if (!array_key_exists($requiredKey, $data)) {
            $valid = false;
            break;
        }
    }
    // Validate field data
    if ($valid) {
        if (!($data['byr'] >= 1920 && $data['byr'] <= 2002)) continue;
        if (!($data['iyr'] >= 2010 && $data['iyr'] <= 2020)) continue;
        if (!($data['eyr'] >= 2020 && $data['eyr'] <= 2030)) continue;
        $heightUnit = substr($data['hgt'], -2);
        $height = intval(substr($data['hgt'], 0, -2));
        switch ($heightUnit) {
            case 'cm': if (!($height >= 150 && $height <= 193)) continue 2; break;
            case 'in': if (!($height >= 59 && $height <= 76)) continue 2; break;
            default: continue 2;
        }
        if (!preg_match('/^#[0-9a-f]{6}$/', $data['hcl'])) continue;
        if (!in_array($data['ecl'], ['amb','blu','brn','gry','grn','hzl','oth'])) continue;
        if (!preg_match('/^[0-9]{9}$/', $data['pid'])) continue;
        print_r($data);
        $numValid++;
    }
}

echo "$numValid\n";