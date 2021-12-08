<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypesSrc;

use InvalidArgumentException;
use ReflectionClass;

abstract class Enum
{
    public string $value;

    public function __construct(string $value)
    {
        $reflection = new ReflectionClass($this);
        if (! in_array($value, $reflection->getConstants())) {
            throw new InvalidArgumentException('Unexpect enum value: ' . $value);
        }

        $this->value = $value;
    }
}
