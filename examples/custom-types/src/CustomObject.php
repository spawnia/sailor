<?php declare(strict_types=1);

namespace Spawnia\Sailor\CustomTypesSrc;

final class CustomObject
{
    public string $foo;

    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }
}
