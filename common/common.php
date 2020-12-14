<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Common functions ////////////////////////////////////////////////////////////////////////////////////////////////////

function greatest_common_divisor(int $n, int $m): int
{
    $n = abs($n);
    $m = abs($m);
    if ($n == 0) return $m;
    if ($m == 0) return $n;
    return $n > $m
        ? greatest_common_divisor($m, $n % $m)
        : greatest_common_divisor($n, $m % $n);
}

function least_common_multiple(int $n, int $m): int
{
    $x = $n;
    for ($y = 0; ; $x += $n) {
        while ($y < $x) {
            $y += $m;
        }
        if ($x == $y) {
            break;
        }
    }
    return $x;
}

function dd()
{
    foreach (func_get_args() as $arg) {
        print_r($arg);
        echo "\n";
    }
    die;
}

function dump()
{
    foreach (func_get_args() as $arg) {
        print_r($arg);
        echo "\n";
    }
}

abstract class AbstractSolution
{
    protected string $rawInput;

    public function solve(int $part, string $inputFilename): string
    {
        $method = 'solvePart' . $part;
        $this->rawInput = file_get_contents($inputFilename);
        return $this->$method();
    }

    abstract protected function solvePart1(): string;

    abstract protected function solvePart2(): string;
}