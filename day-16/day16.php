<?php

class Day16 extends AbstractSolution
{
    protected array $fields = [];
    protected array $myTicket = [];
    protected array $tickets = [];
    protected array $validRanges = [];

    protected function solvePart1(): string
    {
        $this->parseInput();
        $this->calculateValidRanges();
        $sum = 0;
        foreach ($this->tickets as $values) {
            foreach ($values as $value) {
                $valid = false;
                foreach ($this->validRanges as $range) {
                    if ($value >= $range[0] && $value <= $range[1]) {
                        $valid = true;
                        break;
                    }
                }
                if (!$valid) {
                    $sum += $value;
                }
            }
        }
        return $sum;
    }

    protected function solvePart2(): string
    {
        $this->parseInput();
        $this->calculateValidRanges();
        $this->tickets[] = $this->myTicket;
        // Remove invalid tickets
        foreach ($this->tickets as $i => $values) {
            foreach ($values as $value) {
                $valid = false;
                foreach ($this->validRanges as $range) {
                    if ($value >= $range[0] && $value <= $range[1]) {
                        $valid = true;
                        break;
                    }
                }
                if (!$valid) {
                    unset($this->tickets[$i]);
                }
            }
        }
        // Calculate possibilities per field [field index => [possible field keys]]
        $possibilities = array_fill(0, count($this->myTicket), array_keys($this->fields));
        foreach ($this->tickets as $t => $ticket) {
            foreach ($possibilities as $fieldIndex => $fieldPossibilities) {
                $value = $ticket[$fieldIndex];
                foreach ($fieldPossibilities as $f => $field) {
                    $valid = false;
                    $ranges = $this->fields[$field];
                    foreach ($ranges as $range) {
                        if ($value >= $range[0] && $value <= $range[1]) {
                            $valid = true;
                            break;
                        }
                    }
                    if (!$valid) {
                        unset($possibilities[$fieldIndex][$f]);
                    }
                }
            }
        }
        // Remove used from others
        uasort($possibilities, function(array &$a, array &$b) { return count($a) <=> count($b); });
        $used = [];
        $map = [];
        foreach ($possibilities as $fieldIndex => $fields) {
            $diff = array_diff($fields, $used);
            $field = array_pop($diff);
            $map[$field] = $fieldIndex;
            $used[] = $field;
        }
        // Get them departure stats
        $all = 1;
        foreach ($map as $field => $fieldIndex) {
            if (substr($field, 0, 10) == 'departure ') {
                $all *= $this->myTicket[$fieldIndex];
            }
        }
        return $all;
    }

    protected function calculateValidRanges()
    {
        $validRanges = [];
        foreach ($this->fields as $ranges) {
            foreach ($ranges as $range) {
                $inserted = false;
                $insertIndex = 0;
                foreach ($validRanges as $i => $validRange) {
                    // If the range starts after this existing one, continue
                    if ($validRange[1] + 1 < $range[0]) {
                        $insertIndex++;
                        continue;
                    }
                    // If the range ends before this existing one, break
                    if ($validRange[0] - 1 > $range[1]) {
                        break;
                    }
                    // Merge ranges
                    $inserted = true;
                    $validRanges[$i][0] = min($validRange[0], $range[0]);
                    $validRanges[$i][1] = max($validRange[1], $range[1]);
                    while (isset($validRanges[$i + 1]) && $validRanges[$i + 1][0] <= $validRanges[$i][1] + 1) {
                        $validRanges[$i][1] = max($validRanges[$i][1], $validRanges[$i + 1][1]);
                        array_splice($validRanges, $i + 1, 1);
                    }
                    break;
                }
                if (!$inserted) {
                    array_splice($validRanges, $insertIndex, null, [$range]);
                }
            }
        }
        $this->validRanges = $validRanges;
    }

    protected function parseInput()
    {
        $parts = explode("\n\n", $this->rawInput);
        // Field definitions
        $fields = explode("\n", $parts[0]);
        foreach ($fields as $field) {
            $fieldParts = explode(':', $field);
            $fieldName = $fieldParts[0];
            $rangeParts = explode(' or ', $fieldParts[1]);
            $ranges = [];
            foreach ($rangeParts as $rangePart) {
                $range = explode('-', trim($rangePart));
                $ranges[] = [intval($range[0]), intval($range[1])];
            }
            $this->fields[$fieldName] = $ranges;
        }
        // Ticket definition
        $ticketParts = explode("\n", $parts[1]);
        $this->myTicket = array_map('intval', explode(',', $ticketParts[1]));
        // Other ticket definitions
        $otherTickets = explode("\n", $parts[2]);
        array_shift($otherTickets);
        foreach ($otherTickets as $otherTicket) {
            $this->tickets[] = array_map('intval', explode(',', $otherTicket));
        }
    }
}

class Field
{
    public string $name;
    public array $ranges = [];
}