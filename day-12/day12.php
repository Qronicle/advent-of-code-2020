<?php

class Day12 extends AbstractSolution
{
    protected int $x = 0;
    protected int $y = 0;
    protected int $wayPointX = 10;
    protected int $wayPointY = -1;
    protected array $direction = [1, 0];
    protected array $directions = [[0, -1], [1, 0], [0, 1], [-1, 0]];
    protected int $directionIndex = 1;

    protected function solvePart1(): string
    {
        $directions = explode("\n", $this->rawInput);
        foreach ($directions as $directionInfo) {
            $direction = substr($directionInfo, 0, 1);
            $distance = intval(substr($directionInfo, 1));
            switch ($direction) {
                case 'N': $this->y -= $distance; break;
                case 'S': $this->y += $distance; break;
                case 'E': $this->x += $distance; break;
                case 'W': $this->x -= $distance; break;
                case 'L': $this->turn(-1, $distance); break;
                case 'R': $this->turn(1, $distance); break;
                case 'F':
                    $this->x += $distance * $this->direction[0];
                    $this->y += $distance * $this->direction[1];
                    break;
            }
        }
        return abs($this->x) + abs($this->y);
    }

    protected function solvePart2(): string
    {
        $directions = explode("\n", $this->rawInput);
        foreach ($directions as $directionInfo) {
            $direction = substr($directionInfo, 0, 1);
            $distance = intval(substr($directionInfo, 1));
            switch ($direction) {
                case 'N': $this->wayPointY -= $distance; break;
                case 'S': $this->wayPointY += $distance; break;
                case 'E': $this->wayPointX += $distance; break;
                case 'W': $this->wayPointX -= $distance; break;
                case 'L': $this->turnWaypoint(-1, $distance); break;
                case 'R': $this->turnWaypoint(1, $distance); break;
                case 'F':
                    $this->x += $distance * $this->wayPointX;
                    $this->y += $distance * $this->wayPointY;
                    break;
            }
        }
        return abs($this->x) + abs($this->y);
    }

    protected function turn(int $direction, int $amount)
    {
        if ($amount % 90 != 0) {
            throw new Exception('Unexpected rotation amount: ' . $amount);
        }
        $this->directionIndex += $direction * ($amount / 90);
        $this->directionIndex %= 4;
        if ($this->directionIndex < 0) $this->directionIndex = 4 + $this->directionIndex;
        $this->direction = $this->directions[$this->directionIndex];
    }

    protected function turnWaypoint(int $direction, int $amount)
    {
        $increment = ($direction * ($amount / 90)) % 4;
        if ($increment < 0) $increment = 4 + $increment;
        for ($i = 0; $i < $increment; $i++) {
            $newX = -1 * $this->wayPointY;
            $newY = $this->wayPointX;
            $this->wayPointX = $newX;
            $this->wayPointY = $newY;
        }
    }
}