<?php

class Day23 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $cups = array_map('intval', str_split($this->rawInput));
        $highest = max($cups);
        $lowest = min($cups);
        for ($i = 0; $i < 100; $i++) {
            $current = $destination = array_shift($cups);
            $pick = array_splice($cups, 0, 3);
            do {
                if (--$destination < $lowest) {
                    $destination = $highest;
                }
                $destinationIndex = array_search($destination, $cups);
            } while ($destinationIndex === false);
            array_splice($cups, $destinationIndex + 1, 0, $pick);
            $cups[] = $current;
        }
        $oneIndex = array_search(1, $cups);
        return implode('', array_slice($cups, $oneIndex + 1)) . implode('', array_slice($cups, 0, $oneIndex));
    }

    protected function solvePart2(): string
    {
        // Prepare lists
        $cups = array_map('intval', str_split($this->rawInput));
        $nextCupIndexes = [];
        $currentIndex = 0;
        $highest = max($cups);
        $lowest = min($cups);
        for (; $highest < 1000000;) {
            $cups[] = ++$highest;
        }
        for ($i = 0; $i < $highest - 1; $i++) {
            $nextCupIndexes[$i] = $i + 1;
        }
        $nextCupIndexes[$highest - 1] = 0;
        $cupIndexes = array_flip($cups);

        // Play the game (you just lost it)
        for ($i = 0; $i < 10000000; $i++) {
            $pickIndexes = [];
            $picks = [];
            for ($j = 0, $pickIndex = $currentIndex; $j < 3; $j++) {
                $pickIndex = $nextCupIndexes[$pickIndex];
                $pickIndexes[] = $pickIndex;
                $picks[] = $cups[$pickIndex];
            }
            $destination = $cups[$currentIndex];
            do {
                if (--$destination < $lowest) {
                    $destination = $highest;
                }
            } while(in_array($destination, $picks));
            $destinationIndex = $cupIndexes[$destination];
            // The ol' next index switcheroo
            $nextCupIndexes[$currentIndex] = $nextCupIndexes[$pickIndexes[2]];
            $nextCupIndexes[$pickIndexes[2]] = $nextCupIndexes[$destinationIndex];
            $nextCupIndexes[$destinationIndex] = $pickIndexes[0];
            // On to the next one
            $currentIndex = $nextCupIndexes[$currentIndex];
        }

        // Calculate the result
        $oneIndex = $cupIndexes[1];
        $firstValue = $cups[$nextCupIndexes[$oneIndex]];
        $secondValue = $cups[$nextCupIndexes[$nextCupIndexes[$oneIndex]]];

        return $firstValue * $secondValue;
    }
}