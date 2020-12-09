<?php

class Day9 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $input = explode("\n", $this->rawInput);
        return $this->findInvalidNumber($input);
    }

    protected function solvePart2(): string
    {
        $input = explode("\n", $this->rawInput);
        $invalidNumber = $this->findInvalidNumber($input);
        $inputLength = count($input);
        for ($i = 0; $i < $inputLength - 1; $i++) {
            $sum = 0;
            $range = [];
            for ($j = $i; $j < $inputLength; $j++) {
                $sum += $input[$j];
                $range[] = $input[$j];
                if ($sum == $invalidNumber) {
                    return min($range) + max($range);
                }
                if ($sum > $invalidNumber) {
                    continue 2;
                }
            }
        }
        return ':(';
    }

    protected function findInvalidNumber(array $input): ?int
    {
        $inputLength = count($input);
        $preambleLength = 25;
        for ($i = $preambleLength; $i < $inputLength; $i++) {
            $sum = $input[$i];
            $found = false;
            for ($j = $i - $preambleLength; $j < $i - 1; $j++) {
                $a = $input[$j];
                if ($a > $sum) {
                    continue;
                }
                for ($k = $j + 1; $k < $i; $k++) {
                    $b = $input[$k];
                    if ($a + $b == $sum) {
                        $found = true;
                        break 2;
                    }
                }
            }
            if (!$found) {
                return $sum;
            }
        }
        return null;
    }
}