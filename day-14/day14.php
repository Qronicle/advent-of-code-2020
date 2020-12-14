<?php

class Day14 extends AbstractSolution
{
    const BYTES = 36;

    protected array $memory = [];
    protected array $instructions = [];

    protected function solvePart1(): string
    {
        $input = explode("\n", $this->rawInput);
        $mask = null;
        foreach ($input as $line) {
            if ($line[1] == 'a') {
                $mask = str_split(strrev(ltrim(trim(substr($line, 7)), 'X')));
                continue;
            }
            preg_match('/^mem\[([0-9]+)\] = ([0-9]+)$/', $line, $matches);
            $this->memory[$matches[1]] = $this->mask($matches[2], $mask);
        }
        return array_sum($this->memory);
    }

    protected function solvePart2(): string
    {
        $input = explode("\n", $this->rawInput);
        $mask = null;
        foreach ($input as $line) {
            if ($line[1] == 'a') {
                $mask = str_split(strrev(trim(substr($line, 7))));
                continue;
            }
            preg_match('/^mem\[([0-9]+)\] = ([0-9]+)$/', $line, $matches);
            $addresses = $this->maskAddress($matches[1], $mask);
            foreach ($addresses as $address) {
                $this->memory[$address] = intval($matches[2]);
            }
        }
        return array_sum($this->memory);
    }

    protected function mask(int $int, array $mask)
    {
        $bits = str_pad(base_convert($int, 10, 2), self::BYTES, '0', STR_PAD_LEFT);
        foreach ($mask as $i => $maskBit) {
            if ($maskBit == 'X') continue;
            $bits[self::BYTES - $i - 1] = $maskBit;
        }
        return base_convert($bits, 2, 10);
    }

    protected function maskAddress(int $address, array $mask): array
    {
        $bits = str_pad(base_convert($address, 10, 2), self::BYTES, '0', STR_PAD_LEFT);
        $addresses = [$bits];
        foreach ($mask as $i => $maskBit) {
            if ($maskBit == '0') continue;
            $bitPos = self::BYTES - $i - 1;
            if ($maskBit == '1') {
                foreach ($addresses as $i => $address) {
                    $addresses[$i][$bitPos] = 1;
                }
            } else {
                $currentBit = $bits[$bitPos];
                $otherBit = $currentBit == '0' ? '1' : '0';
                $newAddresses = [];
                foreach ($addresses as $address) {
                    $newAddress = $address;
                    $newAddress[$bitPos] = $otherBit;
                    $newAddresses[] = $newAddress;
                }
                $addresses = array_merge($addresses, $newAddresses);
            }
        }
        foreach ($addresses as $i => $address) {
            $addresses[$i] = base_convert($address, 2, 10);
        }
        return $addresses;
    }
}