<?php

class Day11 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $map = new SeatingMap($this->rawInput);
        $map->run();
        return $map->getNumTiles(SeatingMap::TILE_SEAT_OCCUPIED);
    }

    protected function solvePart2(): string
    {
        $map = new SeatingMap($this->rawInput);
        $map->run(SeatingMap::SIGHT_FAR);
        return $map->getNumTiles(SeatingMap::TILE_SEAT_OCCUPIED);
    }
}

class SeatingMap
{
    const TILE_FLOOR         = '.';
    const TILE_SEAT_EMPTY    = 'L';
    const TILE_SEAT_OCCUPIED = '#';

    const SIGHT_NEAR = 1;
    const SIGHT_FAR  = 2;

    protected array $tiles;
    protected int $width;
    protected int $height;

    protected array $directions = [[0, -1], [1, -1], [1, 0], [1, 1], [0, 1], [-1, 1], [-1, 0], [-1, -1]];

    public function __construct(string $input)
    {
        $rows = explode("\n", $input);
        $this->tiles = array_map('str_split', $rows);
        $this->height = count($this->tiles);
        $this->width = count($this->tiles[0]);
    }

    public function run(int $sightType = self::SIGHT_NEAR)
    {
        $mapAsString = $this->toString();
        while (true) {
            $this->tick($sightType);
            $newMapAsString = $this->toString();
            if ($mapAsString == $newMapAsString) {
                return;
            }
            $mapAsString = $newMapAsString;
        }
    }

    protected function tick(int $sightType)
    {
        $newTiles = $this->tiles;
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                switch ($this->tiles[$y][$x]) {
                    case self::TILE_SEAT_EMPTY:
                        $occupiedSeatsAround = false;
                        foreach ($this->directions as $direction) {
                            if ($this->isSeatOccupiedInDirection($x, $y, $direction[0], $direction[1], $sightType)) {
                                $occupiedSeatsAround = true;
                                break;
                            }
                        }
                        if (!$occupiedSeatsAround) {
                            $newTiles[$y][$x] = self::TILE_SEAT_OCCUPIED;
                        }
                        break;
                    case self::TILE_SEAT_OCCUPIED:
                        $numOccupiedSeatsAround = 0;
                        foreach ($this->directions as $direction) {
                            if ($this->isSeatOccupiedInDirection($x, $y, $direction[0], $direction[1], $sightType)) {
                                if (++$numOccupiedSeatsAround == ($sightType == self::SIGHT_NEAR ? 4 : 5)) {
                                    $newTiles[$y][$x] = self::TILE_SEAT_EMPTY;
                                    break;
                                }
                            }
                        }
                        break;
                }
            }
        }
        $this->tiles = $newTiles;
    }

    protected function isSeatOccupiedInDirection(int $seatX, int $seatY, int $dirX, int $dirY, int $sightType): bool
    {
        if ($sightType == self::SIGHT_NEAR) {
            return self::TILE_SEAT_OCCUPIED == ($this->tiles[$seatY + $dirY][$seatX + $dirX] ?? '');
        } else {
            $distance = 1;
            do {
                $tile = $this->tiles[$seatY + ($dirY * $distance)][$seatX + ($dirX * $distance)] ?? false;
                if ($tile == self::TILE_SEAT_OCCUPIED) {
                    return true;
                } elseif ($tile == self::TILE_SEAT_EMPTY) {
                    return false;
                }
                $distance++;
            } while ($tile);
            return false;
        }
    }

    public function getNumTiles(string $tileType): int
    {
        $numTiles = 0;
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $numTiles += $this->tiles[$y][$x] == $tileType ? 1 : 0;
            }
        }
        return $numTiles;
    }

    public function toString(array $tiles = null)
    {
        return implode("\n", array_map(function (array $val): string {
            return implode('', $val);
        }, $tiles ?? $this->tiles));
    }
}