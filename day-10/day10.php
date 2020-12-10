<?php

class Day10 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $input = explode("\n", $this->rawInput);
        sort($input);
        $diffNums = [1 => 0, 2 => 0, 3 => 1];
        foreach ($input as $i => $jolts) {
            $prevJolts = $input[$i - 1] ?? 0;
            $diff = $jolts - $prevJolts;
            if ($diff > 3) die('IMPOSSIBLE');
            $diffNums[$diff]++;
        }
        return $diffNums[1] * $diffNums[3];
    }

    protected array $input;
    protected int $inputLength;
    protected array $numPathsFrom;

    protected function solvePart2(): string
    {
        $this->input = explode("\n", $this->rawInput);
        sort($this->input);
        array_unshift($this->input, 0);
        $this->inputLength = count($this->input);
        $this->numPathsFrom = [];
        return $this->getNumPathsFrom(0);
    }

    protected function getNumPathsFrom(int $index): int
    {
        if ($index == $this->inputLength - 1) {
            return 1;
        }
        if (isset($this->numPathsFrom[$index])) {
            return $this->numPathsFrom[$index];
        }
        $numPaths = 0;
        $startJolts = $this->input[$index];
        for ($i = $index + 1; $i < $this->inputLength; $i++) {
            $jolts = $this->input[$i];
            $diff = $jolts - $startJolts;
            if ($diff > 3) break;
            $numPaths += $this->getNumPathsFrom($i);
        }
        $this->numPathsFrom[$index] = $numPaths;
        return $numPaths;
    }
}