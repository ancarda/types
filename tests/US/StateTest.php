<?php

declare(strict_types=1);

namespace Test;

use \Ancarda\Type\US\State;
use \DomainException;
use \PHPUnit\Framework\TestCase;

final class StateTest extends TestCase
{
    public function testFromCode()
    {
        $tx = new State('tx');
        $this->assertEquals('TX', $tx->code());
        $this->assertEquals('Texas', $tx->name());
        $this->assertEquals('Texas (TX)', (string) $tx);
        $this->assertEquals('{"code":"TX","name":"Texas"}', json_encode($tx));
    }

    public function fromNameProvider()
    {
        return [
            ['ARKANSAS', 'AR'],
            ['aLAbaMa', 'AL'],
            [" \tGA\n", 'GA'],
        ];
    }

    /**
     * @dataProvider fromNameProvider
     */
    public function testFromName($codeName, $expected)
    {
        $ak = new State($codeName);
        $this->assertEquals($expected, $ak->code());
    }

    public function testRejectInvalidStates()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('XX');
        new State('XX');
    }
}
