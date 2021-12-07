<?php declare(strict_types=1);

namespace Spawnia\Sailor\TypeConverter;

use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\TypeConverter;

trait GeneratesTypeConverter
{
    abstract protected function decorateTypeConverter(Type $type, ClassType $class, Method $fromGraphQL, Method $toGraphQL): ClassType;

    protected function makeTypeConverter(Type $type, EndpointConfig $endpointConfig): ClassType
    {
        $class = new ClassType(
            $type->name,
            new PhpNamespace($endpointConfig->typeConvertersNamespace())
        );

        $class->addImplement(TypeConverter::class);

        $fromGraphQL = $class->addMethod('fromGraphQL');
        $fromGraphQL->addParameter('value');

        $toGraphQL = $class->addMethod('toGraphQL');
        $toGraphQL->addParameter('value');

        return $this->decorateTypeConverter($type, $class, $fromGraphQL, $toGraphQL);
    }

    public function typeConverterClassName(Type $type, EndpointConfig $endpointConfig): string
    {
        return $endpointConfig->typeConvertersNamespace() . '\\' . $type->name;
    }
}
