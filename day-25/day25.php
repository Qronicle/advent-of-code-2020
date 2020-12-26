<?php

class Day25 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $publicKeys = explode("\n", $this->rawInput);
        $card = new Thing();
        $card->calculateLoopSize($publicKeys[0]);
        $door = new Thing();
        $door->calculateLoopSize($publicKeys[1]);
        //return $card->createEncryptionKey($door);
        return $door->createEncryptionKey($card);
    }

    protected function solvePart2(): string
    {
    }
}

class Thing
{
    public int $subjectNumber = 7;
    public int $publicKey;
    public int $loopSize;

    public function calculateLoopSize(int $publicKey): int
    {
        $this->publicKey = $publicKey;
        $num = 1;
        $this->loopSize = 0;
        do {
            $this->loopSize++;
            $num *= $this->subjectNumber;
            $num = $num % 20201227;
        } while ($publicKey != $num);
        return $this->loopSize;
    }

    public function createEncryptionKey(Thing $thing): int
    {
        $num = 1;
        for ($i = 0;  $i < $this->loopSize; $i++) {
            $num *= $thing->publicKey;
            $num = $num % 20201227;
        }
        return $num;
    }
}