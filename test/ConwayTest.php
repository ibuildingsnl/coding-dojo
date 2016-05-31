<?php

namespace CodingDojo\Test;

use function igorw\conway\tick;

class ConwayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Wee little learning test to display how tick() works.
     *
     * @test
     * @see https://en.wikipedia.org/wiki/Conway%27s_Game_of_Life#Examples_of_patterns
     */
    public function blinker()
    {
        $generation0 = [
            [0,0,0,0,0],
            [0,0,0,0,0],
            [0,1,1,1,0],
            [0,0,0,0,0],
            [0,0,0,0,0],
        ];
        $generation1 = [
            [0,0,0,0,0],
            [0,0,1,0,0],
            [0,0,1,0,0],
            [0,0,1,0,0],
            [0,0,0,0,0],
        ];

        $this->assertEquals($generation1, tick($generation0));
    }
}
