<?php

declare(strict_types=1);

namespace Test;

use \Ancarda\Type\Network\IPAddress;
use \DomainException;
use \PHPUnit\Framework\TestCase;

final class IPAddressTest extends TestCase
{
    public function testBasic()
    {
        $ip = new IPAddress('abcd:ef01:2345:6789:abcd:ef01:2345:6789');
        $this->assertEquals('abcd:ef01:2345:6789:abcd:ef01:2345:6789', $ip->expanded());
    }

    public function testWillLeftPad()
    {
        $ip = new IPAddress('a:b:c:d:ee:ff:111:2222');
        $this->assertEquals('000a:000b:000c:000d:00ee:00ff:0111:2222', $ip->expanded());
    }

    public function testWillExpandZeros()
    {
        $ip = new IPAddress('2001:db8::1');
        $this->assertEquals('2001:0db8:0000:0000:0000:0000:0000:0001', $ip->expanded());
    }

    public function testRejectMultipleColons()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('An IPv6 address can only have 1 group of collapsed zeros (::).');
        new IPAddress('2001:db8::1::1');
    }

    public function testRejectGarbage()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Input does not look like an IPv6 address.');
        new IPAddress(bin2hex(random_bytes(8)));
    }

    public function minifyAddressProvider()
    {
        return [
            ['2001:0db8:0001:0002:0003:0004:0005:0006', '2001:db8:1:2:3:4:5:6'],
            ['2001:0db8:0101:0234:ff03:9904:0005:0006', '2001:db8:101:234:ff03:9904:5:6'],
        ];
    }

    /**
     * @dataProvider minifyAddressProvider
     */
    public function testMinifyAddress($ipAddress, $expected)
    {
        $ip = new IPAddress($ipAddress);
        $this->assertEquals($expected, $ip->minified());
    }

    public function minifyAddressWithCollapsedZerosProvider()
    {
        return [
            ['2001:db8:0:0:0:0:0:1', '2001:db8::1'],
            ['::1', '::1'],
            ['::1:2:3:4', '::1:2:3:4'],
            ['::', '::'],
            ['1:2:3:4::', '1:2:3:4::'],
        ];
    }

    /**
     * @dataProvider minifyAddressWithCollapsedZerosProvider
     */
    public function testMinifyAddressWithCollapsedZeros($ipAddress, $expected)
    {
        $ip = new IPAddress($ipAddress);

        $this->assertEquals($expected, $ip->minified());
        $this->assertEquals($expected, $ip->value());
    }

    public function testIsLoopback()
    {
        $ip = new IPAddress('::1');
        $this->assertEquals($ip->isLoopback(), true);

        $ip2 = new IPAddress('::2');
        $this->assertEquals($ip2->isLoopback(), false);
    }

    public function testReverseDNS()
    {
        $ip = new IPAddress('2001:db8:1234:5678:9abc:def0:8765:4321');
        $this->assertEquals(
            $ip->reverseDNS(),
            '1.2.3.4.5.6.7.8.0.f.e.d.c.b.a.9.8.7.6.5.4.3.2.1.8.b.d.0.1.0.0.2.ip6.arpa.'
        );

        $ip2 = new IPAddress('::1');
        $this->assertEquals(
            $ip2->reverseDNS(),
            '1.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.ip6.arpa.'
        );
    }
}
