<?php

require __DIR__ . '/../common/common.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$map = new Map($input);

$directions = [
    [1, 1],
    [3, 1],
    [5, 1],
    [7, 1],
    [1, 2],
];

$mTrees = 1;

foreach ($directions as $direction) {
    $x = 0;
    $numTrees = 0;
    for ($y = 0; $y < $map->getHeight(); $y += $direction[1], $x += $direction[0]) {
        $numTrees += $map->isTree($x, $y) ? 1 : 0;
    }
    $mTrees *= $numTrees;
}

echo "$mTrees\n";

class Map
{
    protected array $tiles;

    protected int $width;
    protected int $height;

    const TILE_OPEN = '.';
    const TILE_TREE = '#';

    public function __construct(string $map)
    {
        $this->tiles = explode("\n", $map);
        $this->height = count($this->tiles);
        $this->width = strlen($this->tiles[0]);
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function isTree(int $x, int $y): bool
    {
        return $this->getTile($x, $y) === self::TILE_TREE;
    }

    public function getTile(int $x, $y): string
    {
        if ($y >= count($this->tiles)) throw new OutOfBoundsException();
        $x = $x % $this->width;
        return $this->tiles[$y][$x];
    }
}
