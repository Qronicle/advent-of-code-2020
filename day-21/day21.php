<?php

class Day21 extends AbstractSolution
{
    protected array $food = [];
    protected array $possibleIngredients = [];
    protected array $ingredientAllergens = [];

    protected function solvePart1(): string
    {
        $this->parseInput();
        $this->linkAllergens();
        // Count ingredients without known allergens
        $ingredientAmounts = [];
        foreach ($this->food as $food) {
            foreach ($food->ingredients as $ingredient) {
                $ingredientAmounts[$ingredient] = ($ingredientAmounts[$ingredient] ?? 0) + 1;
            }
        }
        $total = 0;
        foreach ($ingredientAmounts as $ingredient => $amount) {
            if (!isset($this->ingredientAllergens[$ingredient])) {
                $total += $amount;
            }
        }
        return $total;
    }

    protected function solvePart2(): string
    {
        $this->parseInput();
        $this->linkAllergens();
        asort($this->ingredientAllergens);
        return implode(',', array_keys($this->ingredientAllergens));
    }

    protected function parseInput(): void
    {
        $food = explode("\n", $this->rawInput);
        foreach ($food as $foodString) {
            $mainParts = explode(' (contains ', $foodString);
            $this->food[] = (object) [
                'ingredients' => explode(' ', $mainParts[0]),
                'allergens'   => explode(', ', substr($mainParts[1], 0, -1)),
            ];
        }
    }

    protected function linkAllergens(): void
    {
        $foundIngredients = [];
        foreach ($this->food as $food) {
            foreach ($food->allergens as $allergen) {
                $currentPossibleIngredients = $this->possibleIngredients[$allergen] ?? null;
                $newPossibleIngredients = [];
                foreach ($food->ingredients as $ingredient) {
                    $newPossibleIngredients[] = $ingredient;
                }
                if (is_null($currentPossibleIngredients)) {
                    $this->possibleIngredients[$allergen] = $newPossibleIngredients;
                } else {
                    $this->possibleIngredients[$allergen] = array_intersect($currentPossibleIngredients, $newPossibleIngredients);
                }
                if (count($this->possibleIngredients[$allergen]) === 1) {
                    $ingredient = reset($this->possibleIngredients[$allergen]);
                    $foundIngredients[] = $ingredient;
                    $this->ingredientAllergens[$ingredient] = $allergen;
                }
            }
        }
        while (count($foundIngredients)) {
            $newFoundIngredients = [];
            foreach ($this->possibleIngredients as $allergen => $ingredients) {
                foreach ($foundIngredients as $foundIngredient) {
                    if (($key = array_search($foundIngredient, $ingredients)) !== false) {
                        unset($this->possibleIngredients[$allergen][$key]);
                    }
                    if (count($this->possibleIngredients[$allergen]) === 1) {
                        $ingredient = reset($this->possibleIngredients[$allergen]);
                        $newFoundIngredients[] = $ingredient;
                        $this->ingredientAllergens[$ingredient] = $allergen;
                    }
                }
            }
            $foundIngredients = $newFoundIngredients;
        }
    }
}