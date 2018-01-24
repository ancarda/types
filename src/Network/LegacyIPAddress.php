<?php

declare(strict_types=1);

namespace Ancarda\Type\Network;

use \Ancarda\Type\Byte;
use \DomainException;
use \JsonSerializable;

/**
 * Represents a Legacy (IPv4) IP Address.
 *
 * @author  Mark Dain <mark@markdain.net>
 * @license https://choosealicense.com/licenses/mit/ (MIT License)
 */
class LegacyIPAddress implements JsonSerializable
{
    /**
     * @var Byte
     */
    protected $a = null;

    /**
     * @var Byte
     */
    protected $b = null;

    /**
     * @var Byte
     */
    protected $c = null;

    /**
     * @var Byte
     */
    protected $d = null;

    /**
     * Constructs an object that represents an IPv4 address.
     *
     * @param string $ip An IPv4 address, such as 192.0.2.41.
     */
    public function __construct(string $ip)
    {
        if (substr_count($ip, '.') !== 3) {
            throw new DomainException('An IPv4 address must be in the format N.N.N.N');
        }

        list($a, $b, $c, $d) = explode('.', $ip);

        $this->a = new Byte((int) $a);
        $this->b = new Byte((int) $b);
        $this->c = new Byte((int) $c);
        $this->d = new Byte((int) $d);
    }

    /**
     * Returns the IPv4 address, represented as 4 numbers separated by periods.
     *
     * @return string X.X.X.X
     */
    public function value(): string
    {
        return $this->a . '.' . $this->b . '.' . $this->c . '.' . $this->d;
    }

    /**
     * Determines if this IP address is used as the Loopback Address, as
     * defined in RFC5735. Addresses in this subnet send traffic that does not
     * leave the source machine.
     *
     * Most famously, 127.0.0.1 is in this subnet (127.0.0.0/8).
     *
     * @return bool If this IP is in 127.0.0.1/8.
     */
    public function isLoopback(): bool
    {
        return $this->a == '127';
    }

    /**
     * Determines if this IP address represents a Private Network (RFC1918)
     * address or not. For example, anything in 10.*.*.* or 192.168.*.*.
     *
     * This is probably more commonly known as a LAN IP address or Internal IP
     * address, contrasted to an External IP address that's facing the wider
     * Internet.
     *
     * @return bool If defined in RFC1918, Section 3.
     */
    public function isPrivate(): bool
    {
        return
            // 10.0.0.0/8
            ($this->a == '10') ||

            // 192.168.0.0/16
            ($this->a == '192' && $this->b == '168') ||

            // 172.16.0.0/12
            ($this->a == '172' && ($this->b >= '16' && $this->b <= '31'))
        ;
    }

    /**
     * Determines if this IP address is inside a Documentation Prefix (RFC5737)
     * and is thus non-routable.
     *
     * @return bool If defined in RFC5737, Section 3.
     */
    public function isDocumentation(): bool
    {
        return
            // 192.0.2.0/24
            ($this->a == '192' && $this->b == '0' && $this->c == '2') ||

            // 198.51.100.0/24
            ($this->a == '198' && $this->b == '51' && $this->c == '100') ||

            // 203.0.113.0/24
            ($this->a == '203' && $this->b == '0' && $this->c == '113')
        ;
    }

    /**
     * Returns the Fully Qualified Domain Name (FQDN) for this IP address'
     * Reverse DNS entry, suitable to be given to a DNS resolver to look up the
     * PTR value associated.
     *
     * This function reverses the IP address to match DNS' hierarchical nature
     * and adds in-addr.arpa. on the end. The string ends in a period.
     *
     * @return string d.c.b.a.in-addr.arpa.
     */
    public function reverseDNS(): string
    {
        return $this->d . '.' . $this->c . '.' . $this->b . '.' . $this->a .
            '.in-addr.arpa.';
    }

    /**
     * Returns the IPv4 address, represented as 4 numbers separated by periods.
     *
     * @return string X.X.X.X
     */
    public function __toString(): string
    {
        return $this->a . '.' . $this->b . '.' . $this->c . '.' . $this->d;
    }

    /**
     * Returns the IPv4 address, represented as 4 numbers separated by periods.
     *
     * @return string X.X.X.X
     */
    public function jsonSerialize(): string
    {
        return $this->a . '.' . $this->b . '.' . $this->c . '.' . $this->d;
    }
}
