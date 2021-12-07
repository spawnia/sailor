<?php declare(strict_types=1);

namespace Spawnia\Sailor\Convert;

use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\EndpointConfig;

trait GeneratesTypeConverter
{
    abstract protected function decorateTypeConverterClass(Type $type, ClassType $class, Method $fromGraphQL, Method $toGraphQL): ClassType;

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
     * @return class-string<TypeConverter>
     */
    public function typeConverterClassName(Type $type, EndpointConfig $endpointConfig): string
    {
        return $endpointConfig->typeConvertersNamespace() . '\\' . $this->typeConverterBaseName($type);
    }

    protected function typeConverterBaseName(Type $type): string
    {
        return "{$type->name}Converter";
    }
}
