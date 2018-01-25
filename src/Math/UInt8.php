<?php

declare(strict_types=1);

namespace Ancarda\Type\Math;

use \DomainException;
use \JsonSerializable;

/**
 * Encodes an unsigned integer between 0 (0x00) and 255 (0xFF).
 *
 * @author  Mark Dain <mark@markdain.net>
 * @license https://choosealicense.com/licenses/mit/ (MIT License)
 */
class UInt8 implements JsonSerializable
{
    /**
     * @var int
     */
    protected $value = null;

    /**
     * Constructs an object that represents an unsigned 8-bit integer.
     *
     * @param int $value An integer between 0x00 (0) and 0xFF (255).
     * @throws DomainException Value outside [0x00-0xFF].
     */
    public function __construct(int $value)
    {
        if ($value < 0x00) {
            throw new DomainException('UInt8 cannot be lower than 0 (0x00)');
        }
        if ($value > 0xFF) {
            throw new DomainException('UInt8 cannot exceed 255 (0xFF)');
        }

        $this->value = $value;
    }

    /**
     * Returns the underlying value, which is stored as an integer.
     *
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * Returns the underlying value, which is stored as an integer.
     *
     * This function has to return a string so if a UInt8 is coerced into a
     * string, either via `(string) $byte` or by its use where a string is
     * expected, such as concatenation, this will function will be silently
     * called to make this happen.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Returns an integer, making this object suitable for use via json_encode.
     *
     * @return int
     */
    public function jsonSerialize(): int
    {
        return $this->value;
    }
}
