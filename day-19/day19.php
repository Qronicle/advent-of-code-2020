<?php

class Day19 extends AbstractSolution
{
    protected array $rules = [];
    protected array $codes = [];
    protected array $ruleValues = [];
    protected array $ruleRegexes = [];
    protected bool $part2 = false;

    protected function solvePart1(): string
    {
        $this->parseInput();
        $regex = '/^' . $this->generateRegex() . '$/';
        $numValid = 0;
        foreach ($this->codes as $code) {
            if (preg_match($regex, $code)) {
                $numValid++;
            }
        }
        return $numValid;
    }

    protected function solvePart2(): string
    {
        $this->parseInput();
        $this->part2 = true;
        // Update rules the cheaty way
        $this->rules[11] = [];
        for ($i = 0; $i < 5; $i++) {
            $rule11 = [];
            for ($j = 1; $j <= $i + 1; $j++) {
                $rule8[] = 42;
                array_unshift($rule11, 42);
                $rule11[] = 31;
            }
            $this->rules[11][] = $rule11;
        }
        $regex = '/^' . $this->generateRegex() . '$/';
        $numValid = 0;
        foreach ($this->codes as $code) {
            if (preg_match($regex, $code)) {
                $numValid++;
            }
        }
        return $numValid;
    }

    protected function parseInput()
    {
        $inputParts = explode("\n\n", $this->rawInput);
        $ruleStrings = explode("\n", $inputParts[0]);
        $this->codes = explode("\n", $inputParts[1]);
        foreach ($ruleStrings as $ruleString) {
            $parts = explode(': ', $ruleString);
            $ruleId = $parts[0];
            // Single character rule
            if ($parts[1][0] == '"') {
                $this->rules[$ruleId] = $parts[1][1];
                continue;
            }
            // Other rules
            $parts = explode(' | ', $parts[1]);
            $this->rules[$ruleId] = [];
            foreach ($parts as $part) {
                $this->rules[$ruleId][] = explode(' ', $part);
            }
        }
    }

    protected function generateRegex(int $ruleId = 0): string
    {
        if (isset($this->ruleRegexes[$ruleId])) {
            return $this->ruleRegexes[$ruleId];
        }
        $rule = $this->rules[$ruleId];
        if (!is_array($rule)) {
            $this->ruleRegexes[$ruleId] = $rule;
            return $rule;
        }
        $subRegexes = [];
        foreach ($rule as $subRules) {
            $subRegex = '';
            foreach ($subRules as $subRule) {
                $subRegex .= $this->generateRegex($subRule);
            }
            $subRegexes[] = $subRegex;
        }
        if (count($subRegexes) == 1) {
            $result = $subRegexes[0];
            if ($this->part2 && $ruleId == 8) {
                $result = '(' . $result . ')+';
            }
        } else {
            $result = '(' . implode('|', $subRegexes) . ')';
        }
        $this->ruleRegexes[$ruleId] = $result;
        return $result;
    }

    // Old and slow code ///////////////////////////////////////////////////////////////////////////////////////////////

    protected function solvePart1Slow(): string
    {
        $this->parseInput();
        $validCodes = $this->getValidCodesForRuleId(0);
        $numValid = 0;
        foreach ($validCodes as $validCode) {
            foreach ($this->codes as $c => $code) {
                if ($code === $validCode) {
                    $numValid++;
                    unset($this->codes[$c]);
                    break;
                }
            }
        }
        return $numValid;
    }

    protected function getValidCodesForRuleId(int $ruleId): array
    {
        if (isset($this->ruleValues[$ruleId])) {
            return $this->ruleValues[$ruleId];
        }
        $rule = $this->rules[$ruleId];
        if (is_array($rule)) {
            $value = [];
            foreach ($rule as $subRules) {
                $subValues = [''];
                foreach ($subRules as $subRule) {
                    $subSubValues = $this->getValidCodesForRuleId($subRule); // ['aa', 'ab']
                    $newSubValues = [];
                    foreach ($subValues as $i => $subValue) {
                        foreach ($subSubValues as $j => $subSubValue) {
                            $newSubValues[] = $subValue . $subSubValue;
                        }
                    }
                    $subValues = $newSubValues;
                }
                //echo "Rule $ruleId\n";
                //echo "Merge: " . implode(',', $value) . ' with ' . implode(',', $subValues) . "\n";
                $value = array_merge($value, $subValues);
                //echo '> ' . implode(',', $value) . "\n";
            }
            $this->ruleValues[$ruleId] = $value;
        } else {
            $this->ruleValues[$ruleId] = [$rule];
        }
        return $this->ruleValues[$ruleId];
    }
}