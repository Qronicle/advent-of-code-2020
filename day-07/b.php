<?php

require __DIR__ . '/../common/common.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$bagRules = explode("\n", $input);

$luggageProcessing = new LuggageProcessing($bagRules);
$totalBags = $luggageProcessing->getAmountOfChildBags('shiny gold');

echo "$totalBags\n";

class LuggageProcessing
{
    protected array $bags;
    protected array $bagParents;

    public function __construct(array $bagRules)
    {
        $this->parseBagRules($bagRules);
    }

    public function getAmountOfChildBags(string $bagType): int
    {
        $numChildBags = 0;
        $bagsToCheck = [$bagType => 1];
        while ($bagsToCheck) {
            $newBagsToCheck = [];
            foreach ($bagsToCheck as $bag => $amount) {
                if (isset($this->bags[$bag])) {
                    foreach ($this->bags[$bag] as $childBag => $childAmount) {
                        $newBagsToCheck[$childBag] = $newBagsToCheck[$childBag] ?? 0;
                        $newBagsToCheck[$childBag] += $amount * $childAmount;
                        $numChildBags += $amount * $childAmount;
                    }
                }
            }
            $bagsToCheck = $newBagsToCheck;
        }
        return $numChildBags;
    }

    public function getPossibleParentBagsFor(string $bagType): array
    {
        $possibleParents = [];
        $bagsToCheck = [$bagType];
        while ($bagsToCheck) {
            $newBagsToCheck = [];
            foreach ($bagsToCheck as $bag) {
                if (isset($this->bagParents[$bag])) {
                    foreach ($this->bagParents[$bag] as $parentBag => $amount) {
                        if (!isset($possibleParents[$parentBag])) {
                            $possibleParents[$parentBag] = $parentBag;
                            $newBagsToCheck[] = $parentBag;
                        }
                    }
                }
            }
            $bagsToCheck = $newBagsToCheck;
        }
        return array_values($possibleParents);
    }

    protected function parseBagRules(array $bagRules): void
    {
        $this->bags = [];
        $this->bagParents = [];
        foreach ($bagRules as $bagRule) {
            $bagRuleParts = explode(" bags contain ", rtrim($bagRule, '.'));
            $bag = $bagRuleParts[0];
            $childBagDefs = explode(', ', $bagRuleParts[1]);
            $childBags = [];
            foreach ($childBagDefs as $childBagDef) {
                if (preg_match('/^(\d)+ ([a-z ]+) bags?$/', $childBagDef, $matches)) {
                    $childBags[$matches[2]] = intval($matches[1]);
                }
            }
            $this->bags[$bag] = $childBags;
            foreach ($childBags as $childBag => $amount) {
                $this->bagParents[$childBag][$bag] = $amount;
            }
        }
    }
}