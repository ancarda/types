<?php

declare(strict_types=1);

namespace Ancarda\Type\Network;

use \Ancarda\Type\Byte;
use \DomainException;

/**
 * Represents an IPv6 IP Address.
 *
 * @author  Mark Dain <mark@markdain.net>
 * @license https://choosealicense.com/licenses/mit/ (MIT License)
 */
class IPAddress
{
    /**
     * @var array Array of Byte
     */
    protected $ip = [];

    /**
     * Constructs an object that represents an IPv6 address.
     *
     * @param string $ip An IPv6 address such as 2001:db8::af:1bca:ff1c.
     * @throws DomainException Input does not look like an IPv6 address.
     * @throws DomainException An IPv6 address can only have 1 group of collapsed zeros (::).
     */
    public function __construct(string $ip)
    {
        // Prevent garbage strings
        if (substr_count($ip, ':') === 0) {
            throw new DomainException('Input does not look like an IPv6 address.');
        }

        // Enforce 1 or zero double colons.
        if (substr_count($ip, '::') > 1) {
            throw new DomainException('An IPv6 address can only have 1 group of collapsed zeros (::).');
        }

        // Detect double colons to be expanded
        $dcp = strpos($ip, '::');
        if ($dcp !== false) {
            $left = substr($ip, 0, $dcp);
            $right = substr($ip, $dcp + 2);
            $missing = 7 - (substr_count($left, ':') + substr_count($right, ':'));
            $middle = str_repeat(':', $missing);
            $ip = $left . $middle . $right;
        }

        // Left pad each block
        $blocks = array_map(function (string $value): string {
            return str_pad($value, 4, '0', STR_PAD_LEFT);
        }, explode(':', $ip));

        foreach ($blocks as $b) {
            $this->ip[] = new Byte(hexdec($b[0] . $b[1]));
            $this->ip[] = new Byte(hexdec($b[2] . $b[3]));
        }
    }

    /**
     * Returns the IPv6 address, represented as a hexadecimal string separated
     * by colons every 2 bytes, with leading zeros trimmed and the largest
     * consecutive block of zeros replaced with "::".
     *
     * This function is an alias of minified().
     *
     * @return string
     */
    public function value(): string
    {
        return $this->minified();
    }

    /**
     * Returns the smallest possible address that can represent this IPv6
     * address. Leading zeros are trimmed away, and the largest block of
     * consecutive zeros is replaced with "::".
     *
     * @return string 2001:db8::af:1bca:ff1c
     */
    public function minified(): string
    {
        $out = '';
        for ($i = 0; $i <= 15; $i += 2) {
            $a = ltrim(dechex($this->ip[$i]->value()), '0');
            $b = dechex($this->ip[$i + 1]->value());
            $out .= $a . (!empty($a) && strlen($b) == 1 ? '0' . $b : $b);
            if ($i < 14) {
                $out .= ':';
            }
        }

        $i = 7;
        $zeroL = strpos($out, '0') === 0 ? '0' : '';
        $zeroT = substr($out, strlen($out)-1, 1) == '0' ? '0' : '';
        while (true) {
            $out = str_replace(substr($zeroL . str_repeat(':0', $i) . $zeroT, 0, -1), '::', $out, $count);
            if ($count !== 0) {
                return $out;
            }
            $i--;
            if ($i === 1) {
                break;
            }
        }

        return $out;
    }

    /**
     * Returns a fully expanded IPv6 address, where each block is zero padded.
     *
     * @return string 2001:0db8:0000:0000:00af:1bca:ff1c.
     */
    public function expanded(): string
    {
        $out = '';
        for ($i = 0; $i <= 15; $i += 2) {
            $out .= str_pad(dechex($this->ip[$i]->value()), 2, '0', STR_PAD_LEFT);
            $out .= str_pad(dechex($this->ip[$i + 1]->value()), 2, '0', STR_PAD_LEFT);
            if ($i < 14) {
                $out .= ':';
            }
        }

        return $out;
    }

    /**
     * Determines if this IP address is used as the Loopback Address, as
     * defined in RFC4291, Section 2.5.3. Traffic sent to this address does not
     * leave the source machine.
     *
     * @return bool If this address is equal to ::1 (/128).
     */
    public function isLoopback(): bool
    {
        return
            ($this->ip[0]->value() === 0) &&
            ($this->ip[1]->value() === 0) &&
            ($this->ip[2]->value() === 0) &&
            ($this->ip[3]->value() === 0) &&
            ($this->ip[4]->value() === 0) &&
            ($this->ip[5]->value() === 0) &&
            ($this->ip[6]->value() === 0) &&
            ($this->ip[7]->value() === 0) &&
            ($this->ip[8]->value() === 0) &&
            ($this->ip[9]->value() === 0) &&
            ($this->ip[10]->value() === 0) &&
            ($this->ip[11]->value() === 0) &&
            ($this->ip[12]->value() === 0) &&
            ($this->ip[13]->value() === 0) &&
            ($this->ip[14]->value() === 0) &&
            ($this->ip[15]->value() === 1);
    }
}
