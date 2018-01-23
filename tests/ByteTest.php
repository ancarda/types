<?php

declare(strict_types=1);

namespace Test;

use \Ancarda\Type\Byte;
use \DomainException;
use \PHPUnit\Framework\TestCase;

final class ByteTest extends TestCase
{
    public function testBasic()
    {
        $test = random_int(1, 254);

        $b = new Byte($test);
        $this->assertEquals($b->value(), $test);
        $this->assertEquals((string) $b, "$test");
        $this->assertEquals(json_encode($b), "$test");
    }

    public function testRejectHighValues()
    {
        $this->expectException(DomainException::class);
        $b = new Byte(random_int(256, 65535));
    }

    public function testRejectLowValues()
    {
        $this->expectException(DomainException::class);
        $b = new Byte(random_int(-65535, -1));
    }
}
