<?php

class Day13 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $input = explode("\n", $this->rawInput);
        $startTime = $input[0];
        $factors = array_filter(explode(',', $input[1]), 'is_numeric');
        $earliestFactor = null;
        $earliestOffset = null;
        foreach ($factors as $factor) {
            $nextOffset = ($factor - $startTime % $factor) % $factor;
            if ($earliestFactor === null || $nextOffset < $earliestOffset) {
                $earliestFactor = $factor;
                $earliestOffset = $nextOffset;
            }
        }
        return $earliestFactor * $earliestOffset;
    }

    protected function solvePart2(): string
    {
        $input = explode("\n", $this->rawInput);
        $lines = explode(',', $input[1]);
        $primes = array_filter($lines, 'is_numeric');

        $totalOffset = 0;
        $prevOffset = 0;
        $prevPrime = $primes[0];
        unset($primes[0]);
        foreach ($primes as $offset => $prime) {
            $time = $totalOffset ? $totalOffset : $prevPrime;
            $relativeOffset = $offset - $prevOffset;
            while (true) {
                if (($time + $relativeOffset) % $prime == 0) {
                    break;
                }
                $time += $prevPrime;
            }
            $prevOffset = $offset;
            $prevPrime = $prime * $prevPrime;
            $totalOffset = $time + $relativeOffset;
        }
        return $totalOffset - $prevOffset;
    }

    protected function solvePart2Slow(): string
    {
        $input = explode("\n", $this->rawInput);
        $lines = explode(',', $input[1]);
        $factors = array_filter($lines, 'is_numeric');
        $highestFactor = -1;
        $highestFactorIndex = null;
        foreach ($factors as $i => $factor) {
            if ($factor > $highestFactor) {
                $highestFactor = $factor;
                $highestFactorIndex = $i;
            }
        }
        $lineOffsets = [];
        foreach ($lines as $i => $line) {
            if (!is_numeric($line) || $line == $highestFactor) continue;
            $lineOffsets[$line] = $i - $highestFactorIndex;
        }

        $t = 0;
        while (true) {
            $time = ++$t * $highestFactor;
            foreach ($lineOffsets as $line => $offset) {
                if (($time + $offset) % $line != 0) {
                    continue 2;
                }
            }
            return $time + reset($lineOffsets);
        }
    }
}