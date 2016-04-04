<?php

namespace CodingDojo\Test;

use CodingDojo\Foo;

class FooTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function when_asked_to_foo_it_bars()
    {
        $foo = new Foo();
        $this->assertSame('bar', $foo->foo());
    }
}
