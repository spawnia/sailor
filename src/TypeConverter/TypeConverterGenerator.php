<?php declare(strict_types=1);

namespace Spawnia\Sailor\TypeConverter;

use GraphQL\Type\Definition\Type;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Spawnia\Sailor\Codegen\ClassGenerator;
use Spawnia\Sailor\EndpointConfig;
use Spawnia\Sailor\TypeConverter;

abstract class TypeConverterGenerator extends ClassGenerator
{
    abstract protected function decorate(Type $type, ClassType $class, Method $fromGraphQL, Method $toGraphQL): ClassType;

    public function forType(Type $type): ClassType
    {
        $class = new ClassType(
            $type->name,
            new PhpNamespace(self::typeConvertersNamespace($this->endpointConfig))
        );

        $class->addImplement(TypeConverter::class);

        $fromGraphQL = $class->addMethod('fromGraphQL');
        $fromGraphQL->addParameter('value');

        $toGraphQL = $class->addMethod('toGraphQL');
        $toGraphQL->addParameter('value');

        return $this->decorate($type, $class, $fromGraphQL, $toGraphQL);
    }

    public static function className(string $typeName, EndpointConfig $endpointConfig): string
    {
        return self::typeConvertersNamespace($endpointConfig) . '\\' . $typeName;
    }

    protected static function typeConvertersNamespace(EndpointConfig $endpointConfig): string
    {
        return $endpointConfig->namespace() . '\\TypeConverters';
    }
}
