<?php

namespace CodingDojo\Test;

class GildedRoseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_still_works()
    {
        $dir = __DIR__;
        $actual = `php $dir/GildedRose/texttest_fixture.php`;
        $expected = file_get_contents("$dir/GildedRose/texttest_fixture.txt");

        $this->assertSame($expected, $actual);
    }
}
