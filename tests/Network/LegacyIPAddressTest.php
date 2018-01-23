<?php

declare(strict_types=1);

namespace Test;

use \Ancarda\Type\Network\LegacyIPAddress;
use \DomainException;
use \PHPUnit\Framework\TestCase;

final class LegacyIPAddressTest extends TestCase
{
    public function testBasic()
    {
        $ip = new LegacyIPAddress('192.168.0.1');
        $this->assertEquals($ip->value(), '192.168.0.1');
        $this->assertEquals((string) $ip, '192.168.0.1');
        $this->assertEquals(json_encode($ip), '"192.168.0.1"');

        $ip2 = new LegacyIPAddress('10.45.18.31');
        $this->assertEquals($ip2->value(), '10.45.18.31');
    }

    public function testRejectLargeNumbers()
    {
        $this->expectException(DomainException::class);
        new LegacyIPAddress('10.0.0.259');
    }

    public function testRejectNegativeNumbers()
    {
        $this->expectException(DomainException::class);
        new LegacyIPAddress('-1.0.0.0');
    }

    public function testRejectGarbage()
    {
        $this->expectException(DomainException::class);
        new LegacyIPAddress(bin2hex(random_bytes(8)));
    }

    public function testLoopback()
    {
        $localhost = new LegacyIPAddress('127.0.0.1');
        $this->assertEquals($localhost->isLoopback(), true);

        $one_nine_two = new LegacyIPAddress('192.168.0.0');
        $this->assertEquals($one_nine_two->isLoopback(), false);
    }

    public function testRfc1918()
    {
        $ten = new LegacyIPAddress('10.0.0.0');
        $this->assertEquals($ten->isPrivate(), true);

        $one_nine_two = new LegacyIPAddress('192.168.0.0');
        $this->assertEquals($one_nine_two->isPrivate(), true);

        $complexA = new LegacyIPAddress('172.16.0.0');
        $complexB = new LegacyIPAddress('172.31.255.255');
        $complexC = new LegacyIPAddress('172.32.0.0');
        $complexD = new LegacyIPAddress('172.15.255.255');
        $this->assertEquals($complexA->isPrivate(), true);
        $this->assertEquals($complexB->isPrivate(), true);
        $this->assertEquals($complexC->isPrivate(), false);
        $this->assertEquals($complexD->isPrivate(), false);

        $random = new LegacyIPAddress('198.51.100.104');
        $this->assertEquals($random->isPrivate(), false);
    }

    public function testRfc5737()
    {
        $one = new LegacyIPAddress('192.0.2.0');
        $two = new LegacyIPAddress('198.51.100.0');
        $three = new LegacyIPAddress('203.0.113.0');
        $random = new LegacyIPAddress('10.8.41.12');

        $this->assertEquals($one->isDocumentation(), true);
        $this->assertEquals($two->isDocumentation(), true);
        $this->assertEquals($three->isDocumentation(), true);
        $this->assertEquals($random->isDocumentation(), false);
    }

    public function testReverseDNS()
    {
        $ip = new LegacyIPAddress('172.31.18.42');
        $this->assertEquals($ip->reverseDNS(), '42.18.31.172.in-addr.arpa.');
    }
}
