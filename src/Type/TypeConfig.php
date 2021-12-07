<?php declare(strict_types=1);

namespace Spawnia\Sailor\Type;

use Nette\PhpGenerator\ClassType;
use Spawnia\Sailor\Convert\TypeConverter;

/**
 * Specifies how Sailor should deal with a GraphQL type.
 */
interface TypeConfig
{
    /**
     * Fully qualified class name of a TypeConverter instance.
     *
     * @return class-string<TypeConverter>
     */
    public function typeConverter(): string;

    /**
     * Reference to the type for usage in PHPDocs, e.g. string, \Foo\Bar.
     *
     * Make sure that class names begin with a backslash and are thus fully qualified.
     */
    public function typeReference(): string;

    /**
     * Return any number of generated class definitions to write to files.
     *
     * @return iterable<ClassType>
     */
    public function generateClasses(): iterable;
}
