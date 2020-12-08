<?php

class Day8 extends AbstractSolution
{
    protected function solvePart1(): string
    {
        $processor = new Processor(explode("\n", $this->rawInput));
        try {
            $processor->run();
        } catch (InfiniteLoopException $ex) {
            return $processor->getAcc();
        }
    }

    protected function solvePart2(): string
    {
        $instructions = explode("\n", $this->rawInput);
        $processor = new Processor([]);
        $prevUpdatedIndex = null;
        $prevUpdatedCommand = null;
        $swapCommands = ['nop', 'jmp'];
        for ($i = 0; $i < count($instructions); $i++) {
            $command = substr($instructions[$i], 0, 3);
            if (!in_array($command, $swapCommands)) {
                continue;
            }
            // Reset previous command
            if ($prevUpdatedCommand) {
                $instructions[$prevUpdatedIndex] = $prevUpdatedCommand;
            }
            $prevUpdatedIndex = $i;
            $prevUpdatedCommand = $instructions[$i];
            // Swap command
            $otherCommand = $command == $swapCommands[0] ? $swapCommands[1] : $swapCommands[0];
            $instructions[$i] = $otherCommand . substr($instructions[$i], 3);
            // Run code
            try {
                $processor->init($instructions);
                $processor->run();
            } catch (InfiniteLoopException $ex) {
                continue;
            }
            return $processor->getAcc();
        }
        return 'NO SOLUTION';
    }
}

class Processor
{
    protected array $instructions;
    protected int $currentInstruction;
    protected int $acc;
    protected array $executedInstructions;

    public function __construct(array $instructions)
    {
        $this->init($instructions);
    }

    public function init(array $instructions)
    {
        $this->instructions = $instructions;
        $this->reset();
    }

    public function run()
    {
        while (true) {
            try {
                $this->tick();
            } catch (ProcessTerminated $terminated) {
                break;
            }
        }
    }

    public function reset()
    {
        $this->currentInstruction = 0;
        $this->acc = 0;
        $this->executedInstructions = [];
    }

    public function tick()
    {
        if (isset($this->executedInstructions[$this->currentInstruction])) {
            throw new InfiniteLoopException();
        }
        $this->executedInstructions[$this->currentInstruction] = true;
        $instruction = $this->getInstruction();
        switch ($instruction->operation) {
            case 'acc':
                $this->acc += $instruction->argument;
                break;
            case 'jmp':
                $this->currentInstruction += $instruction->argument;
                return;
            case 'nop':
                break;
            default:
                throw new UnknownCommandException();
        }
        $this->currentInstruction++;
    }

    public function getAcc(): int
    {
        return $this->acc;
    }

    protected function getInstruction(int $index = null): stdClass
    {
        $index = $index ?? $this->currentInstruction;
        if ($index == count($this->instructions)) {
            throw new ProcessTerminated();
        }
        $instructionString = $this->instructions[$index] ?? null;
        if (!$instructionString) {
            throw new OutOfBoundsException();
        }
        return $this->parseInstruction($instructionString);
    }

    protected function parseInstruction(string $instruction): stdClass
    {
        $parts = explode(' ', $instruction);
        return (object)[
            'operation' => $parts[0],
            'argument'  => intval($parts[1]),
        ];
    }
}

// Custom exceptions
class InfiniteLoopException extends Exception {}
class UnknownCommandException extends Exception {}
class ProcessTerminated extends Exception {}