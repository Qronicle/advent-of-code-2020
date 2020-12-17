<?php

class Day17 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $pocketDimension = new PocketDimension($this->rawInput);
        $pocketDimension->run(6);
        return $pocketDimension->getNumActiveCubes();
    }

    protected function solvePart2(): string
    {
        $pocketDimension = new PocketDimension4D($this->rawInput);
        $pocketDimension->run(6);
        return $pocketDimension->getNumActiveCubes();
    }
}

class PocketDimension
{
    protected array $tiles = [];
    protected array $bounds = [];

    public function __construct(string $input)
    {
        $rows = explode("\n", $input);
        foreach ($rows as $y => $row) {
            $this->tiles[0][$y] = array_map(function (string $val) {
                return $val == '#';
            }, str_split($row));
        }
        $this->bounds['z'] = [0, 0];
        $this->bounds['y'] = [0, count($rows) - 1];
        $this->bounds['x'] = [0, strlen($rows[0]) - 1];
    }

    public function run(int $steps)
    {
        for ($step = 0; $step < $steps; $step++) {
            $this->tick();
        }
    }

    public function getNumActiveCubes(): int
    {
        $numActive = 0;
        foreach ($this->tiles as $z => &$zTiles) {
            foreach ($zTiles as $y => &$yTiles) {
                foreach ($yTiles as $x => $value) {
                    $numActive += $value ? 1 : 0;
                }
            }
        }
        return $numActive;
    }

    protected function tick()
    {
        $newTiles = $this->tiles;
        $newBounds = $this->bounds;
        for ($z = $this->bounds['z'][0] - 1; $z <= $this->bounds['z'][1] + 1; $z++) {
            for ($y = $this->bounds['y'][0] - 1; $y <= $this->bounds['y'][1] + 1; $y++) {
                for ($x = $this->bounds['x'][0] - 1; $x <= $this->bounds['x'][1] + 1; $x++) {
                    $isActive = $this->tiles[$z][$y][$x] ?? false;
                    $numActiveNeighbours = $this->getNumActiveNeighbours($x, $y, $z);
                    if ($isActive) {
                        if ($numActiveNeighbours != 2 && $numActiveNeighbours != 3) {
                            $newTiles[$z][$y][$x] = false;
                        }
                    } else {
                        if ($numActiveNeighbours == 3) {
                            $newTiles[$z][$y][$x] = true;
                            // Update bounds
                            $newBounds['z'] = [min($newBounds['z'][0], $z), max($newBounds['z'][1], $z)];
                            $newBounds['y'] = [min($newBounds['y'][0], $y), max($newBounds['y'][1], $y)];
                            $newBounds['x'] = [min($newBounds['x'][0], $x), max($newBounds['x'][1], $x)];
                        }
                    }
                }
            }
        }
        $this->tiles = $newTiles;
        $this->bounds = $newBounds;
    }

    protected function getNumActiveNeighbours(int $x, int $y, int $z): int
    {
        $numActive = 0;
        for ($nz = $z - 1; $nz <= $z + 1; $nz++) {
            for ($ny = $y - 1; $ny <= $y + 1; $ny++) {
                for ($nx = $x - 1; $nx <= $x + 1; $nx++) {
                    if ($nx == $x && $ny == $y && $nz == $z) continue;
                    $numActive += !empty($this->tiles[$nz][$ny][$nx]);
                }
            }
        }
        return $numActive;
    }

    public function __toString()
    {
        $str = "----------------\n\n";
        for ($z = $this->bounds['z'][0]; $z <= $this->bounds['z'][1]; $z++) {
            $str .= "$z\n";
            for ($y = $this->bounds['y'][0]; $y <= $this->bounds['y'][1]; $y++) {
                for ($x = $this->bounds['x'][0]; $x <= $this->bounds['x'][1]; $x++) {
                    $str .= empty($this->tiles[$z][$y][$x]) ? '.' : '#';
                }
                $str .= "\n";
            }
            $str .= "\n";
        }
        return $str;
    }
}

class PocketDimension4D
{
    protected array $tiles = [];
    protected array $bounds = [];

    public function __construct(string $input)
    {
        $rows = explode("\n", $input);
        foreach ($rows as $y => $row) {
            $this->tiles[0][0][$y] = array_map(function (string $val) {
                return $val == '#';
            }, str_split($row));
        }
        $this->bounds['w'] = [0, 0];
        $this->bounds['z'] = [0, 0];
        $this->bounds['y'] = [0, count($rows) - 1];
        $this->bounds['x'] = [0, strlen($rows[0]) - 1];
    }

    public function run(int $steps)
    {
        for ($step = 0; $step < $steps; $step++) {
            $this->tick();
        }
    }

    public function getNumActiveCubes(): int
    {
        $numActive = 0;
        foreach ($this->tiles as &$wTiles) {
            foreach ($wTiles as &$zTiles) {
                foreach ($zTiles as &$yTiles) {
                    foreach ($yTiles as $value) {
                        $numActive += $value ? 1 : 0;
                    }
                }
            }
        }
        return $numActive;
    }

    protected function tick()
    {
        $newTiles = $this->tiles;
        $newBounds = $this->bounds;
        for ($w = $this->bounds['w'][0] - 1; $w <= $this->bounds['w'][1] + 1; $w++) {
            for ($z = $this->bounds['z'][0] - 1; $z <= $this->bounds['z'][1] + 1; $z++) {
                for ($y = $this->bounds['y'][0] - 1; $y <= $this->bounds['y'][1] + 1; $y++) {
                    for ($x = $this->bounds['x'][0] - 1; $x <= $this->bounds['x'][1] + 1; $x++) {
                        $isActive = $this->tiles[$w][$z][$y][$x] ?? false;
                        $numActiveNeighbours = $this->getNumActiveNeighbours($x, $y, $z, $w);
                        if ($isActive) {
                            if ($numActiveNeighbours != 2 && $numActiveNeighbours != 3) {
                                $newTiles[$w][$z][$y][$x] = false;
                            }
                        } else {
                            if ($numActiveNeighbours == 3) {
                                $newTiles[$w][$z][$y][$x] = true;
                                // Update bounds
                                $newBounds['w'] = [min($newBounds['w'][0], $w), max($newBounds['w'][1], $w)];
                                $newBounds['z'] = [min($newBounds['z'][0], $z), max($newBounds['z'][1], $z)];
                                $newBounds['y'] = [min($newBounds['y'][0], $y), max($newBounds['y'][1], $y)];
                                $newBounds['x'] = [min($newBounds['x'][0], $x), max($newBounds['x'][1], $x)];
                            }
                        }
                    }
                }
            }
        }
        $this->tiles = $newTiles;
        $this->bounds = $newBounds;
    }

    protected function getNumActiveNeighbours(int $x, int $y, int $z, int $w): int
    {
        $numActive = 0;
        for ($nw = $w - 1; $nw <= $w + 1; $nw++) {
            for ($nz = $z - 1; $nz <= $z + 1; $nz++) {
                for ($ny = $y - 1; $ny <= $y + 1; $ny++) {
                    for ($nx = $x - 1; $nx <= $x + 1; $nx++) {
                        if ($nx == $x && $ny == $y && $nz == $z && $nw == $w) continue;
                        $numActive += !empty($this->tiles[$nw][$nz][$ny][$nx]);
                    }
                }
            }
        }
        return $numActive;
    }
}