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
        $this->assertEquals($tx->code(), 'TX');
        $this->assertEquals($tx->name(), 'Texas');
        $this->assertEquals((string) $tx, 'Texas (TX)');
        $this->assertEquals(json_encode($tx), '{"code":"TX","name":"Texas"}');
    }

    public function testFromName()
    {
        $ak = new State('ARKANSAS');
        $this->assertEquals($ak->code(), 'AR');

        // Test that it works with lowercase and inconsistent case
        $al = new State('aLAbaMa');
        $this->assertEquals($al->code(), 'AL');

        // Test trim
        $ga = new State(" \tGA\n");
        $this->assertEquals($ga->code(), 'GA');
    }

    public function testRejectInvalidStates()
    {
        $this->expectException(DomainException::class);
        $xx = new State('XX');
    }
}
