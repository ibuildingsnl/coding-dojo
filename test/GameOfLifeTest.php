<?php

use CodingDojo\GameOfLifeCliApplication;
use PHPUnit_Framework_TestCase as TestCase;

class GameOfLifeTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_throw_exception_when_no_filename_argument_given()
    {
        $this->expectException('InvalidArgumentException');
        new GameOfLifeCliApplication([]);
    }

    /**
     * @test
     */
    public function it_should_fail_when_the_provided_filename_could_not_be_loaded()
    {
        $this->expectException('RuntimeException');
        new GameOfLifeCliApplication(['non-existing-file.gol']);
    }
}
