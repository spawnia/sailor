<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Spawnia\Sailor\Codegen\ClassGenerator;
use Spawnia\Sailor\TypeConverter;

class DefaultTypeConfig implements TypeConfig
{
    /** @var class-string<TypeConverter> */
    public string $typeConverter;

    /**
     * Reference to the type, e.g. string, \Foo\Bar.
     */
    public string $typeReference;

    public ?ClassGenerator $classGenerator;

    /**
     * @param  class-string<TypeConverter>  $typeConverter
     */
    public function __construct(string $typeConverter, string $typeReference, ClassGenerator $classGenerator = null)
    {
        $this->typeConverter = $typeConverter;
        $this->typeReference = $typeReference;
        $this->classGenerator = $classGenerator;
    }

    public function typeConverter(): string
    {
        return $this->typeConverter;
    }

    public function typeReference(): string
    {
        return $this->typeReference;
    }

    public function generate(): iterable
    {
        if (isset($this->classGenerator)) {
            return $this->classGenerator->generate();
        }

        return [];
    }
}
