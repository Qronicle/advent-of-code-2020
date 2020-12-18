<?php

class Day18 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $expressions = explode("\n", $this->rawInput);
        $sum = 0;
        foreach ($expressions as $expression) {
            $result[0] = 0;
            $operator[0] = '+';
            $level = 0;
            $exprLen = strlen($expression);
            for ($i = 0; $i < $exprLen; $i++) {
                $char = $expression[$i];
                switch ($char) {
                    case ' ': continue 2;
                    case '+': $operator[$level] = '+'; continue 2;
                    case '*': $operator[$level] = '*'; continue 2;
                    case '(': $result[++$level] = 0; $operator[$level] = '+'; continue 2;
                    case ")":
                        switch ($operator[--$level]) {
                            case '+': $result[$level] += $result[$level+1]; break;
                            case '*': $result[$level] *= $result[$level+1]; break;
                        }
                        continue 2;
                    default:
                        // parse number
                        $number = $char;
                        for ($j = $i+1; $j < $exprLen; $j++) {
                            if (is_numeric($expression[$j])) {
                                $number .= $expression[$j];
                            } else {
                                break;
                            }
                        }
                        $i += strlen($number) - 1;
                        // operation
                        switch ($operator[$level]) {
                            case '+': $result[$level] += intval($number); break;
                            case '*': $result[$level] *= intval($number); break;
                        }
                }
            }
            $sum += $result[0];
        }
        return $sum;
    }

    protected function solvePart2(): string
    {
        $expressions = explode("\n", $this->rawInput);
        $sum = 0;
        foreach ($expressions as $expression) {
            $sum += $this->evaluateExpression($expression);
        }
        return $sum;
    }

    protected function evaluateExpression(string $expression)
    {
        $sub = 0;
        $subStart = null;
        for ($i = 0; $i < strlen($expression); $i++) {
            if ($expression[$i] == '(') {
                if (!$sub) $subStart = $i;
                $sub++;
            } elseif ($expression[$i] == ')') {
                if (--$sub == 0) {
                    $subResult = $this->evaluateExpression(substr($expression, $subStart + 1, $i - $subStart - 1));
                    $expression = substr_replace($expression, $subResult, $subStart, $i - $subStart + 1);
                    $i = $subStart + strlen($subResult);
                }
            }
        }
        $sums = explode(' * ', $expression);
        $result = 1;
        foreach ($sums as $i => $sum) {
            $result *= array_sum(explode(' + ', $sum));
        }
        return $result;
    }
}