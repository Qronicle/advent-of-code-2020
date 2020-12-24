<?php

class Day24 extends AbstractSolution
{
    protected array $grid = [];

    protected array $directions = [
        'nw' => [-1, -1],
        'ne' => [0, -1],
        'e'  => [1, 0],
        'se' => [1, 1],
        'sw' => [0, 1],
        'w'  => [-1, 0],
    ];

    protected function solvePart1(): string
    {
        $paths = explode("\n", $this->rawInput);
        $grid = [];
        foreach ($paths as $path) {
            $x = $y = 0;
            for ($i = 0; $i < strlen($path); $i++) {
                $direction = $path[$i];
                if ($direction === 'n' || $direction === 's') {
                    $direction .= $path[++$i];
                }
                $x += $this->directions[$direction][0];
                $y += $this->directions[$direction][1];
            }
            if (isset($grid["$x,$y"])) {
                unset($grid["$x,$y"]);
            } else {
                $grid["$x,$y"] = true;
            }
        }
        $this->grid = $grid;
        return count($grid);
    }

    protected function solvePart2(): string
    {
        $this->solvePart1();
        for ($day = 1; $day <= 100; $day++) {
            $newGrid = [];
            $whiteTiles = [];
            // Check all black tiles and make list of surrounding white tile coordinates
            foreach ($this->grid as $coordsString => $tmp) {
                $coords = explode(',', $coordsString);
                $numBlack = 0;
                foreach ($this->directions as $direction) {
                    $x = $coords[0] + $direction[0];
                    $y = $coords[1] + $direction[1];
                    if (isset($this->grid["$x,$y"])) {
                        $numBlack++;
                    } else {
                        $whiteTiles["$x,$y"] = [$x, $y];
                    }
                }
                if ($numBlack > 0 && $numBlack <= 2) {
                    $newGrid[$coordsString] = true;
                }
            }
            // Check interesting white tiles
            foreach ($whiteTiles as $coordsString => $coords) {
                // skip black tiles
                if (isset($this->grid[$coordsString])) continue;
                $numBlack = 0;
                foreach ($this->directions as $direction) {
                    $x = $coords[0] + $direction[0];
                    $y = $coords[1] + $direction[1];
                    if (isset($this->grid["$x,$y"])) {
                        $numBlack++;
                    }
                }
                if ($numBlack == 2) {
                    $newGrid[$coordsString] = true;
                }
            }
            $this->grid = $newGrid;
        }
        return count($this->grid);
    }
}