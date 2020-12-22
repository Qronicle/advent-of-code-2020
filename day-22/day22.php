<?php

class Day22 extends AbstractSolution
{
    protected array $hands = [];
    protected array $previousHands = [];
    protected int $winner;

    protected function solvePart1(): string
    {
        $this->parseInput();
        while ($this->playRegular());
        return $this->calculateScore();
    }

    protected function solvePart2(): string
    {
        $this->parseInput();
        $this->hands = [$this->hands];
        while ($this->playRecursive());
        $this->hands = $this->hands[0];
        return $this->calculateScore();
    }

    protected function playRecursive(int $level = 0): bool
    {
        // Check for matching previous hands
        $jsonHands = json_encode($this->hands[$level]);
        if (isset($this->previousHands[$level][$jsonHands])) {
            $this->winner = 1;
            return false;
        }
        // Compare cards!
        $cards = [];
        $topCardPlayer = null;
        foreach ($this->hands[$level] as $player => &$hand) {
            $topCard = array_shift($this->hands[$level][$player]);
            if (is_null($topCard)) continue;
            $cards[$player] = $topCard;
            if ($topCardPlayer === null || $cards[$player] > $cards[$topCardPlayer]) {
                $topCardPlayer = $player;
            }
        }
        // Finish the game when only one card was played
        if (count($cards) == 1) {
            array_unshift($this->hands[$level][$topCardPlayer], $cards[$topCardPlayer]);
            $this->winner = $topCardPlayer;
            return false;
        }
        // Check for recursive game
        $playRecursive = true;
        foreach ($cards as $player => $value) {
            if ($value > count($this->hands[$level][$player])) {
                $playRecursive = false;
                break;
            }
        }
        // Play recursive
        if ($playRecursive) {
            $nextLevel = $level + 1;
            foreach ($this->hands[$level] as $player => &$hand) {
                $this->hands[$nextLevel][$player] = array_slice($hand, 0, $cards[$player]);
            }
            while ($this->playRecursive($nextLevel));
            $topCardPlayer = $this->winner;
            unset($this->hands[$nextLevel]);
            unset($this->previousHands[$nextLevel]);
        }
        // Add cards to winner
        $firstPlayer = key($cards);
        if ($firstPlayer !== $topCardPlayer) {
            $cards = array_reverse($cards);
        }
        foreach ($cards as $card) {
            $this->hands[$level][$topCardPlayer][] = $card;
        }
        $this->previousHands[$level][$jsonHands] = true;
        return true;
    }

    protected function playRegular(): bool
    {
        // Compare cards!
        $cards = [];
        $topCardPlayer = null;
        foreach ($this->hands as $player => &$hand) {
            $topCard = array_shift($this->hands[$player]);
            if (is_null($topCard)) continue;
            $cards[$player] = $topCard;
            if ($topCardPlayer === null || $cards[$player] > $cards[$topCardPlayer]) {
                $topCardPlayer = $player;
            }
        }
        // Finish the game when only one card was played
        if (count($cards) == 1) {
            array_unshift($this->hands[$topCardPlayer], $cards[$topCardPlayer]);
            $this->winner = $topCardPlayer;
            return false;
        }
        // Add cards to winner
        rsort($cards);
        foreach ($cards as $card) {
            $this->hands[$topCardPlayer][] = $card;
        }
        return true;
    }

    protected function calculateScore(): int
    {
        $cards = $this->hands[$this->winner];
        $numCards = count($cards);
        $score = 0;
        for ($i = 0; $i < $numCards; $i++) {
            $score += $cards[$i] * ($numCards - $i);
        }
        return $score;
    }

    protected function parseInput(): void
    {
        $players = explode("\n\n", $this->rawInput);
        foreach ($players as $playerHand) {
            $playerHand = explode("\n", $playerHand);
            $playerId = substr(array_shift($playerHand), 7, -1);
            $this->hands[$playerId] = $playerHand;
        }
    }
}