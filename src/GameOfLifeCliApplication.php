<?php

namespace CodingDojo;

use function igorw\conway\tick;

final class GameOfLifeCliApplication
{
    private $arguments;

    public function __construct(array $arguments)
    {
        if (count($arguments) === 0) {
            throw new \InvalidArgumentException();
        }

        $this->arguments = $arguments;
    }

    public function execute($numberOfGenerations)
    {
        $grid = GenerationLoader::fromFile($this->arguments[0]);
        $printer = new GenerationPrinter();

        while ($numberOfGenerations--) {
            system('clear');
            print $printer->print($grid) . PHP_EOL;

            $grid = tick($grid);
            usleep(100 * 1000);
        }
    }
}
