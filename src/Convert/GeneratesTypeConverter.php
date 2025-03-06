<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

use GraphQL\Type\Definition\NamedType;
use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\EndpointConfig;

trait GeneratesTypeConverter
{
    /** @param Type&NamedType $type */
    abstract protected function decorateTypeConverterClass(Type $type, ClassType $class, Method $fromGraphQL, Method $toGraphQL): ClassType;

    /** @param Type&NamedType $type */
    protected function makeTypeConverter(Type $type, EndpointConfig $endpointConfig): ClassType
    {
        $class = new ClassType(
            $this->typeConverterBaseName($type),
            new PhpNamespace($endpointConfig->typeConvertersNamespace())
        );

        $class->addImplement(TypeConverter::class);

        $fromGraphQL = $class->addMethod('fromGraphQL');
        $fromGraphQL->addParameter('value');

        $toGraphQL = $class->addMethod('toGraphQL');
        $toGraphQL->addParameter('value');

        return $this->decorateTypeConverterClass($type, $class, $fromGraphQL, $toGraphQL);
    }

    /**
     * @param Type&NamedType $type
     *
     * @return class-string<TypeConverter>
     */
    public function typeConverterClassName(Type $type, EndpointConfig $endpointConfig): string
    {
        $namespace = $endpointConfig->typeConvertersNamespace();
        $className = $this->typeConverterBaseName($type);

        return "{$namespace}\\{$className}"; // @phpstan-ignore return.type (class-string not inferred)
    }

    /** @param Type&NamedType $type */
    protected function typeConverterBaseName(Type $type): string
    {
        $namedTypeName = $type->name;
        assert(is_string($namedTypeName));

        return "{$namedTypeName}Converter";
    }
}
