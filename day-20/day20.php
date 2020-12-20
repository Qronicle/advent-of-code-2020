<?php

class Day20 extends AbstractSolution
{
    /** @var Tile[] */
    protected array $tiles = [];

    protected function solvePart1(): string
    {
        $this->parseInput();
        $this->findMatchingTiles();
        $m = 1;
        foreach ($this->tiles as $tile) {
            if ($tile->getNumMatches() === 2) {
                $m *= $tile->id;
            }
        }
        return $m;
    }

    protected array $grid = [];
    protected int $gridSize;
    protected array $seaMonster = [];
    protected array $seaMonsterBounds = [];
    protected array $directions = [[-1, 0], [0, 1], [1, 0], [0, -1]];

    protected function solvePart2(): string
    {
        $this->parseInput();
        $this->findMatchingTiles();
        $this->gridSize = sqrt(count($this->tiles));
        $this->placeTile(0, 0, $this->tiles);
        $this->rotateTiles();
        $this->mergeGrid();
        $this->initSeaMonster();
        foreach (Tile::$mirrors as $mirror) {
            foreach (Tile::$rotations as $rotation) {
                $numSeaMonsters = $this->findAllSeaMonsters();
                if ($numSeaMonsters) {
                    break 2;
                }
                $this->rotateSeaMonster();
            }
            $this->flipSeaMonster();
        }
        $numHashTags = 0;
        foreach ($this->grid as $row) {
            $numHashTags += count(explode('#', $row)) - 1;
        }
        return $numHashTags;
    }

    protected function findAllSeaMonsters(): int
    {
        $numFound = 0;
        $gridSize = count($this->grid);
        for ($y = 0; $y <= $gridSize - $this->seaMonsterBounds[1]; $y++) {
            for ($x = 0; $x <= $gridSize - $this->seaMonsterBounds[0]; $x++) {
                foreach ($this->seaMonster as $coords) {
                    if ($this->grid[$y + $coords[1]][$x + $coords[0]] !== '#') {
                        continue 2;
                    }
                }
                // Unset sea monster tiles
                foreach ($this->seaMonster as $coords) {
                    $this->grid[$y + $coords[1]][$x + $coords[0]] = 'S';
                }
                $numFound++;
            }
        }
        return $numFound;
    }

    protected function printFullGrid()
    {
        foreach ($this->grid as $row) {
            for ($y = 0; $y < Tile::SIZE; $y++) {
                /*if ($y == 0) {
                    foreach ($row as $tile) {
                        echo $tile->id . ' R' . $tile->getRotation() . ' ' . ($tile->getMirrored() ? 'M' : ' ') . '  ';
                    }
                    echo "\n";
                }*/
                foreach ($row as $tile) {
                    echo $tile->rowToString($y) . ' ';
                }
                echo "\n";
            }
            echo "\n";
        }
    }

    protected function initSeaMonster(): void
    {
        $seaMonsterString = '                  # ' . "\n"
                          . '#    ##    ##    ###' . "\n"
                          . ' #  #  #  #  #  #   ';
        foreach (explode("\n", $seaMonsterString) as $y => $seaMonsterLine) {
            foreach (str_split($seaMonsterLine) as $x => $char) {
                if ($char === '#') {
                    $this->seaMonster[] = [$x, $y];
                }
            }
        }
        $this->seaMonsterBounds = [20, 3];
    }

    protected function rotateSeaMonster(): void
    {
        $newSeaMonster = [];
        //$test = [];
        foreach ($this->seaMonster as $coords) {
            $newSeaMonster[] = [
                $this->seaMonsterBounds[1] - $coords[1] - 1,
                $coords[0],
            ];
            //$test[$coords[0]][$this->seaMonsterBounds[1] - $coords[1] - 1] = '#';
        }
        $this->seaMonster = $newSeaMonster;
        $this->seaMonsterBounds = array_reverse($this->seaMonsterBounds);
        /*for ($y = 0; $y < $this->seaMonsterBounds[1]; $y++) {
            for ($x = 0; $x < $this->seaMonsterBounds[0]; $x++) {
                echo $test[$y][$x] ?? '.';
            }
            echo "\n";
        }
        die;*/
    }

    protected function flipSeaMonster(): void
    {
        $newSeaMonster = [];
        foreach ($this->seaMonster as $coords) {
            $newSeaMonster[] = [
                $coords[0],
                $this->seaMonsterBounds[1] - $coords[1] - 1,
            ];
        }
        $this->seaMonster = $newSeaMonster;
    }

    protected function mergeGrid(): void
    {
        $mergedGrid = [];
        foreach ($this->grid as $row) {
            for ($y = 1; $y < Tile::LAST; $y++) {
                $line = '';
                foreach ($row as $tile) {
                    $line .= substr($tile->rowToString($y), 1, -1);
                }
                $mergedGrid[] = $line;
            }
        }
        $this->grid = $mergedGrid;
    }

    /**
     * @param int    $x
     * @param int    $y
     * @param Tile[] $availableTiles
     */
    protected function placeTile(int $x, int $y, array $availableTiles)
    {
        $numNeighbours =
            ($y > 0 ? 1 : 0) + // top
            ($x < $this->gridSize - 1 ? 1 : 0) + // right
            ($y < $this->gridSize - 1 ? 1 : 0) + // bottom
            ($x > 0 ? 1 : 0); // left
        $nextX = $x + 1 == $this->gridSize ? 0 : $x + 1;
        $nextY = $nextX == 0 ? $y + 1 : $y;
        foreach ($availableTiles as $t => $tile) {
            if ($tile->getNumMatches() == $numNeighbours) {
                // Check left and top side can match
                if ($x > 0 && !$tile->hasMatchesForTile($this->grid[$y][$x-1])) continue;
                if ($y > 0 && !$tile->hasMatchesForTile($this->grid[$y-1][$x])) continue;
                $this->grid[$y][$x] = $tile;
                unset($availableTiles[$t]);
                if (!count($availableTiles)) {
                    return true;
                }
                if ($this->placeTile($nextX, $nextY, $availableTiles)) {
                    return true;
                }
                $availableTiles[$t] = $tile;
            }
        }
        die('Impossible');
    }

    protected function rotateTiles(): void
    {
        // Check right side
        for ($y = 0; $y < $this->gridSize; $y++) {
            for ($x = 0; $x < $this->gridSize; $x++) {
                $tile = $this->grid[$y][$x];
                // Get normalized neighbours
                $neighbours = [];
                foreach ($this->directions as $side => $pos) {
                    $neighbour = $this->grid[$y + $pos[0]][$x + $pos[1]] ?? null;
                    if (!$neighbour) continue;
                    $neighbours[$side] = $neighbour->id;
                }
                $tile->place($neighbours);
            }
        }
    }

    protected function parseInput(): void
    {
        $inputParts = explode("\n\n", $this->rawInput);
        foreach ($inputParts as $inputPart) {
            $lines = explode("\n", $inputPart);
            $idLine = array_shift($lines);
            $id = intval(substr($idLine, 5, -1));
            $raw = array_map('str_split', $lines);
            $this->tiles[] = new Tile($id, $raw);
        }
    }

    protected function findMatchingTiles(): void
    {
        $numTiles = count($this->tiles);
        for ($i = 0; $i < $numTiles - 1; $i++) {
            for ($j = $i + 1; $j < $numTiles; $j++) {
                $this->tiles[$i]->findMatches($this->tiles[$j]);
            }
        }
    }

}

class Tile
{
    const SIZE = 10;
    const LAST = 9;

    public static array $rotations = [0, 1, 2, 3];
    public static array $mirrors = [0, 1];

    public int $id;
    protected array $raw;
    protected int $isMirrored = 0;
    protected int $rotation = 0;
    public bool $inPlace = false;

    protected array $borders = [];
    protected array $tileMatches = [];

    public function __construct(int $id, array $raw)
    {
        $this->id = $id;
        $this->raw = $raw;
        // top
        $this->borders[0][0] = implode('', $raw[0]);
        // right
        $this->borders[0][1] = implode('', array_map(function (array $arr): string {
            return $arr[self::LAST];
        }, $raw));
        // bottom
        $this->borders[0][2] = strrev(implode('', $raw[self::LAST]));
        // left
        $this->borders[0][3] = strrev(implode('', array_map(function (array $arr): string {
            return $arr[0];
        }, $raw)));
        // mirrored
        $this->borders[1][0] = strrev($this->borders[0][0]);
        $this->borders[1][1] = strrev($this->borders[0][3]);
        $this->borders[1][2] = strrev($this->borders[0][2]);
        $this->borders[1][3] = strrev($this->borders[0][1]);
    }

    public function findMatches(Tile $tile)
    {
        foreach ($this->borders as $mirrored => &$borders) {
            foreach ($borders as $side => $border) {
                foreach ($tile->borders as $otherMirrored => &$otherBorders) {
                    foreach ($otherBorders as $otherSide => $otherBorder) {
                        if ($border !== strrev($otherBorder)) continue;
                        //$mirrorOffset = $mirrored != $otherMirrored ? 1 : 0;
                        $this->tileMatches[$tile->id][$mirrored][] = $side;
                        $tile->tileMatches[$this->id][$otherMirrored][] = $otherSide;
                    }
                }
            }
        }
    }

    public function __toString(): string
    {
        $str = "Tile $this->id\n";
        $str .= "   Rot: $this->rotation - Mirrored: $this->isMirrored\n";
        foreach ($this->tileMatches as $tileId => $tiles) {
            foreach ($tiles as $mirrored => $sides) {
                foreach ($sides as $side) {
                    $str .= " > Side $side ($mirrored) matches Tile $tileId side $side\n";
                }
            }
        }
        $str .= "\n";
        for ($row = 0; $row < self::SIZE; $row++) {
            $str .= $this->rowToString($row) . "\n";
        }
        return $str;
    }

    public function rowToString(int $row): string
    {
        $string = '';
        switch ($this->rotation) {
            case 0: $string = implode('', $this->raw[$row]); break;
            case 2: $string = strrev(implode('', $this->raw[self::LAST - $row])); break;
            case 1:
                $string = '';
                for ($y = 0; $y < self::SIZE; $y++) {
                    $string .= $this->raw[self::LAST - $y][$row];
                }
                break;
            case 3:
                $string = '';
                for ($y = 0; $y < self::SIZE; $y++) {
                    $string .= $this->raw[$y][self::LAST - $row];
                }
        }
        return $this->isMirrored ? strrev($string) : $string;
    }

    public function getNormalizedNeighbours(int $firstNeighbour): array
    {
        $normalizedNeighbours = [];
        foreach (self::$mirrors as $mirrored) {
            $neighbours = [];
            foreach ($this->tileMatches as $tileId => $tiles) {
                foreach ($tiles[$mirrored] as $side) {
                    $neighbours[$side] = $tileId;
                }
            }
            $start = array_search($firstNeighbour, $neighbours);
            $normalized = [];
            foreach ($neighbours as $side => $tileId) {
                $key = $side - $start;
                if ($key < 0) $key += 4;
                $normalized[$key] = $tileId;
            }
            ksort($normalized);
            $normalizedNeighbours[$mirrored] = $normalized;
        }
        return $normalizedNeighbours;
    }

    public function place(array $neighbours): void
    {
        $rotation = key($neighbours);
        $normalizedNeighbours = normalize_array($neighbours);
        $normalizedNeighboursJson = json_encode($normalizedNeighbours);
        $firstNeighbour = $normalizedNeighbours[0];
        $possibleNeighbours = $this->getNormalizedNeighbours($firstNeighbour);
        foreach ($possibleNeighbours as $mirrored => $possibleNeighbour) {
            if ($normalizedNeighboursJson === json_encode($possibleNeighbour)) {
                $this->isMirrored = $mirrored;
                foreach ($this->tileMatches[$firstNeighbour][$mirrored] as $side) {
                    if ($mirrored) {
                        $this->rotation = ($side - $rotation + 4) % 4;
                    } else {
                        $this->rotation = ($rotation - $side + 4) % 4;
                    }
                    return;
                }
            }
        }
        throw new Exception('Does not happen');
    }

    public function hasMatchesForTile(Tile $tile): bool
    {
        return !empty($this->tileMatches[$tile->id]);
    }

    public function getNumMatches(): int
    {
        return count($this->tileMatches);
    }

    public function getRotation(): int
    {
        return $this->rotation;
    }

    public function getMirrored(): int
    {
        return $this->isMirrored;
    }
}

function normalize_array(array $array): array
{
    $normalized = [];
    ksort($array);
    $smallest = key($array);
    foreach ($array as $key => $value) {
        $normalized[$key - $smallest] = $value;
    }
    return $normalized;
}