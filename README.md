# ancarda/types

_PHP classes that encapsulate data validation around less precise types_

[![Latest Stable Version](https://poser.pugx.org/ancarda/types/v/stable)](https://packagist.org/packages/ancarda/types)
[![Total Downloads](https://poser.pugx.org/ancarda/types/downloads)](https://packagist.org/packages/ancarda/types)
[![License](https://poser.pugx.org/ancarda/types/license)](https://choosealicense.com/licenses/mit/)
[![Build Status](https://travis-ci.org/ancarda/types.svg?branch=master)](https://travis-ci.org/ancarda/types)
[![Coverage Status](https://coveralls.io/repos/github/ancarda/types/badge.svg?branch=master)](https://coveralls.io/github/ancarda/types?branch=master)

types is a library for PHP 7.0+ that encapsulates validation logic in the constructor of small classes, allowing these already-validated, well-defined instances to be passed around. A "type" here can be anything that has reasonable validation logic, such as an IP address class that wraps a string. By centralizing validation to that class constructor, the whole application can accept and emit an IP address instance knowing the sender and consumer (respectively) don't need to do any validation on what would have been previously a string.

This is not a new concept; ancarda/types tries to define many common, useful types that can be extended in your application.

These classes can be used with any framework and have no dependencies other than the built-in JSON extension. This library may be installed via composer with the following command:

	composer require ancarda/types

Types can then be initalized, like so:

```php
<?php

use \Ancarda\Type\Math\UInt8;
$b = new UInt8(102);
// $b can now be passed to a function that accepts UInt8.
// Consumer doesn't need to check $b is 0 to 255.
```
