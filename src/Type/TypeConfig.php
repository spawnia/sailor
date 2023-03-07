<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Nette\PhpGenerator\ClassLike;
use Spawnia\Sailor\Convert\TypeConverter;

/**
 * Specifies how Sailor should deal with a GraphQL type.
 */
interface TypeConfig
{
    /**
     * Fully qualified class name of a TypeConverter instance.
     *
     * The class must allow instantiation with a parameter-less constructor.
     *
     * @return class-string<TypeConverter>
     */
    public function typeConverter(): string;

    /**
     * Return any number of generated class definitions to write to files.
     *
     * @return iterable<ClassLike>
     */
    public function generateClasses(): iterable;
}
