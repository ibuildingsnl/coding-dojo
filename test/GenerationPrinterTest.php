<?php

use CodingDojo\GenerationPrinter;

class GenerationPrinterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_a_generation_based_on_a_grid()
    {
        $grid = [
            [0, 0, 0,],
            [1, 1, 1,],
            [0, 0, 0,],
        ];

        $expectedGeneration = "...
***
...
";
        $generationPrinter = new GenerationPrinter();
        $actualGeneration = $generationPrinter->print($grid);

        $this->assertSame($expectedGeneration, $actualGeneration);
    }
}
