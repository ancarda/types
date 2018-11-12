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
        $this->assertEquals('192.168.0.1', $ip->value());
        $this->assertEquals('192.168.0.1', (string) $ip);
        $this->assertEquals('"192.168.0.1"', json_encode($ip));

        $ip2 = new LegacyIPAddress('10.45.18.31');
        $this->assertEquals($ip2->value(), '10.45.18.31');
    }

    public function testRejectLargeNumbers()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('UInt8 cannot exceed 255 (0xFF)');
        new LegacyIPAddress('10.0.0.259');
    }

    public function testRejectNegativeNumbers()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('UInt8 cannot be lower than 0 (0x00)');
        new LegacyIPAddress('-1.0.0.0');
    }

    public function testRejectGarbage()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('An IPv4 address must be in the format N.N.N.N');
        new LegacyIPAddress(bin2hex(random_bytes(8)));
    }

    public function testLoopbackOnIsPrivate()
    {
        $localhost = new LegacyIPAddress('127.0.0.1');
        $this->assertTrue($localhost->isLoopback());
    }

    public function testLoopbackOnIsNotPrivate()
    {
        $one_nine_two = new LegacyIPAddress('192.168.0.0');
        $this->assertFalse($one_nine_two->isLoopback());
    }

    public function rfc1918OnIsPrivateProvider()
    {
        return [
            ['10.0.0.0'],
            ['192.168.0.0'],
            ['172.16.0.0'],
            ['172.31.255.255'],
        ];
    }

    /**
     * @dataProvider rfc1918OnIsPrivateProvider
     */
    public function testRfc1918OnIsPrivate($ipAddress)
    {
        $legacyIpAddress = new LegacyIPAddress($ipAddress);
        $this->assertTrue($legacyIpAddress->isPrivate());
    }

    public function rfc1918OnIsNotPrivateProvider()
    {
        return [
            ['172.32.0.0'],
            ['172.15.255.255'],
            ['198.51.100.104'],
        ];
    }

    /**
     * @dataProvider rfc1918OnIsNotPrivateProvider
     */
    public function testRfc1918OnIsNotPrivate($ipAddress)
    {
        $legacyIpAddress = new LegacyIPAddress($ipAddress);
        $this->assertFalse($legacyIpAddress->isPrivate());
    }

    public function rfc5737OnIsDocumentationProvider()
    {
        return [
            ['192.0.2.0'],
            ['198.51.100.0'],
            ['203.0.113.0'],
        ];
    }

    /**
     * @dataProvider rfc5737OnIsDocumentationProvider
     */
    public function testRfc5737OnIsDocumentation($ipAddress)
    {
        $legacyIpAddress = new LegacyIPAddress($ipAddress);
        $this->assertTrue($legacyIpAddress->isDocumentation());
    }

    public function testRfc5737OnIsNotDocumentation()
    {
        $random = new LegacyIPAddress('10.8.41.12');
        $this->assertFalse($random->isDocumentation());
    }

    public function testReverseDNS()
    {
        $ip = new LegacyIPAddress('172.31.18.42');
        $this->assertEquals('42.18.31.172.in-addr.arpa.', $ip->reverseDNS());
    }
}
