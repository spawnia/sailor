<?php

declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\TypeConverter;

class TypeConfig
{
    /** @var class-string<TypeConverter> */
    public string $typeConverter;

    /**
     * Reference to the type, e.g. string, \Foo\Bar.
     */
    public string $typeReference;

    /**
     * @param  class-string<TypeConverter>  $typeConverter
     */
    public function __construct(string $typeConverter, string $typeReference)
    {
        $this->typeConverter = $typeConverter;
        $this->typeReference = $typeReference;
    }
}
