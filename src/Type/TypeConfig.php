<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\TypeConverter;

interface TypeConfig
{
    /** @return class-string<TypeConverter> */
    public function typeConverter(): string;

    /**
     * Reference to the type, e.g. string, \Foo\Bar.
     */
    public function typeReference(): string;

    /**
     * @return iterable<ClassType>
     */
    public function generate(): iterable;
}
