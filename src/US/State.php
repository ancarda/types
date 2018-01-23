<?php

declare(strict_types=1);

namespace Ancarda\Type\US;

use \DomainException;
use \JsonSerializable;

/**
 * A state in the United States of America.
 *
 * @author  Mark Dain <mark@markdain.net>
 * @license https://choosealicense.com/licenses/mit/ (MIT License)
 */
class State implements JsonSerializable
{
    /**
     * @var string
     */
    protected $code = null;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * Constructs an object representing a state in the United
     * States of America.
     *
     * @param string $input A U.S. state name or code.
     */
    public function __construct(string $input)
    {
        $input = trim($input);

        $mapping = [
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
        ];

        // Is input a U.S. state by code?
        $_upper = strtoupper($input);
        if (isset($mapping[$_upper])) {
            $this->code = $_upper;
            $this->name = $mapping[$_upper];
            return;
        }

        // Is input a U.S. state by name? Scan for states.
        $_ucinput = ucwords(strtolower($input));
        foreach ($mapping as $code => $name) {
            if ($name === $_ucinput) {
                $this->code = $code;
                $this->name = $name;
                return;
            }
        }

        // Input wasn't recognized or isn't a valud U.S. state.
        throw new DomainException($input);
    }

    /**
     * Returns the 2 character code, e.g. TX for Texas.
     *
     * @return string TX, AR, AL, GA, ...
     */
    public function code(): string
    {
        return $this->code;
    }

    /**
     * Returns the real name of the state, e.g. Alabama.
     *
     * @return string Texas, Arkansas, Alabama, Georgia, ...
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns a string representation that includes the name of
     * the state and the code, e.g. `Texas (TX)`.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * Returns an object that makes this class suitable for use via
     * json_encode. When encoded, an array containing both the 2 character
     * code and the name is returned. For example:
     *
     * ```
     * {"code":"TX","name":"Texas"}
     * ```
     *
     * This function may be overridden in a sub-class if your application needs
     * a different JSON representation.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}
