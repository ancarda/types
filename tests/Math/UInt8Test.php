<?php

declare(strict_types=1);

namespace Test;

use \Ancarda\Type\Math\UInt8;
use \DomainException;
use \PHPUnit\Framework\TestCase;

final class UInt8Test extends TestCase
{
    public function testBasic()
    {
        $test = random_int(1, 254);

        $b = new UInt8($test);
        $this->assertEquals($b->value(), $test);
        $this->assertEquals((string) $b, "$test");
        $this->assertEquals(json_encode($b), "$test");
    }

    public function testRejectHighValues()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('UInt8 cannot exceed 255 (0xFF)');
        new UInt8(random_int(256, 65535));
    }

    public function testRejectLowValues()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('UInt8 cannot be lower than 0 (0x00)');
        new UInt8(random_int(-65535, -1));
    }
}
