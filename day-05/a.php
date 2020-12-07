<?php

require __DIR__ . '/../common/common.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$seats = explode("\n", $input);

$maxSeatId = 0;
foreach ($seats as $seat) {
    $seatId = getSeatId($seat);
    $maxSeatId = max($maxSeatId, $seatId);
}

echo "$maxSeatId\n";

function getSeatId(string $positionString)
{
    $row = findPosition(substr($positionString, 0, 7), 0, 127, 'F', 'B');
    $col = findPosition(substr($positionString, 7), 0, 7, 'L', 'R');
    return $row * 8 + $col;
}

function findPosition(string $positionDef, int $min, int $max, string $loChar, string $upChar): int
{
    $positionDef = str_split($positionDef);
    foreach ($positionDef as $position) {
        if ($position == $loChar) {
            $max = $min + floor(($max - $min) * .5);
        } elseif ($position == $upChar) {
            $min = $max - floor(($max - $min) * .5);
        } else {
            throw new Exception('Invalid positioning character: ' . $position);
        }
    }
    if ($min != $max) {
        throw new Exception("Impossibru: $min > $max");
    }
    return $min;
}