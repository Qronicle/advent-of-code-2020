<?php

class Day15 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        return $this->memory(2020);
    }

    protected function solvePart2(): string
    {
        return $this->memory(30000000);
    }

    protected function memory(int $step): int
    {
        $input = explode(',', $this->rawInput);
        $num = array_pop($input);
        $ages = [];
        $age = 1;
        foreach ($input as $int) {
            $ages[$int] = $age++;
        }
        while ($age < $step) {
            $nextNum = isset($ages[$num]) ? $age - $ages[$num] : 0;
            $ages[$num] = $age++;
            $num = $nextNum;
        }
        return $num;
    }
}